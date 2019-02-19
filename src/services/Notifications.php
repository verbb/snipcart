<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\services;

use Craft;
use craft\mail\Message;

/**
 * Sends notifications as things happen. Currently just email.
 *
 * @package workingconcept\snipcart\services
 * @todo Make a proper Notification model
 */
class Notifications extends \craft\base\Component
{
    // Private Variables
    // =========================================================================

    /**
     * @var
     */
    private $_htmlEmailTemplate;

    /**
     * @var
     */
    private $_textEmailTemplate;

    /**
     * @var bool Whether supplied email template paths are provided by the site
     *           (frontend) or the control panel. Important for switching the
     *           view's template mode when rendering HTML.
     */
    private $_emailTemplatesAreFrontEnd;

    /**
     * @var string Not used. Yet.
     */
    private $_slackWebhook;

    /**
     * @var mixed Variables that should be fed to the notification template.
     */
    private $_notificationVars;

    /**
     * @var array
     */
    private $_errors = [];


    // Public Methods
    // =========================================================================

    /**
     * @param mixed $data
     */
    public function setNotificationVars($data)
    {
        $this->_notificationVars = $data;
    }

    /**
     * @return mixed
     */
    public function getNotificationVars()
    {
        return $this->_notificationVars;
    }

    /**
     * @param $errors
     */
    public function setErrors($errors)
    {
        $this->_errors = $errors;
    }

    /**
     * @param string $htmlTemplate
     * @param string $textTemplate
     * @param bool   $frontend
     */
    public function setEmailTemplate($htmlTemplate, $textTemplate = null, $frontend = false)
    {
        $this->_htmlEmailTemplate = $htmlTemplate;
        $this->_textEmailTemplate = $textTemplate;
        $this->_emailTemplatesAreFrontEnd = $frontend;
    }

    /**
     * @param string $url
     */
    public function setSlackWebhook($url)
    {
        $this->_slackWebhook = $url;
    }

    /**
     * @param array  $to       Array of email addresses
     * @param string $subject  Email subject
     * @return bool `true` on success, `false` otherwise (see ->getErrors())
     * @throws \yii\base\Exception
     */
    public function sendEmail($to, $subject): bool
    {
        $errors        = [];
        $emailSettings = Craft::$app->getProjectConfig()->get('email');
        $view          = Craft::$app->getView();

        /**
         * Switch template mode only if we need to rely on our own template.
         */
        if ($this->_emailTemplatesAreFrontEnd === false)
        {
            /**
             * Remember what we started with.
             */
            $originalTemplateMode = $view->getTemplateMode();

            /**
             * Explicitly set to CP mode.
             *
             * This could technically throw an exception over an invalid
             * template mode, but we're not worried.
             */
            $view->setTemplateMode($view::TEMPLATE_MODE_CP);
        }

        /**
         * Make sure we've got valid templates now that we've
         * switched the view mode.
         */
        if ( ! $this->_ensureTemplatesExist())
        {
            $this->setErrors([ 'Email template(s) are missing.' ]);
            return false;
        }

        // render the HTML message
        $messageHtml = $view->renderPageTemplate(
            $this->_htmlEmailTemplate,
            $this->getNotificationVars()
        );

        // inline the message's styles so they're more likely to be applied
        $emogrifier = new \Pelago\Emogrifier($messageHtml);
        $mergedHtml = $emogrifier->emogrify();

        $messageText = '';

        if ($this->_textEmailTemplate)
        {
            $messageText = $view->renderPageTemplate(
                $this->_textEmailTemplate,
                $this->getNotificationVars()
            );
        }

        foreach ($to as $address)
        {
            $message = new Message();

            $message->setFrom([
                $emailSettings['fromEmail'] => $emailSettings['fromName']
            ]);

            $message->setTo($address);
            $message->setSubject($subject);
            $message->setHtmlBody($mergedHtml);

            if ($messageText)
            {
                $message->setTextBody($messageText);
            }

            if ( ! Craft::$app->mailer->send($message))
            {
                $problem = "Notification failed to send to {$address}!";
                Craft::warning($problem, 'snipcart');
                $errors[] = $problem;
            }
        }

        if (
            $this->_emailTemplatesAreFrontEnd === false &&
            isset($originalTemplateMode)
        )
        {
            $view->setTemplateMode($originalTemplateMode);
        }

        $this->setErrors($errors);

        return count($errors) === 0;
    }


    // Private Methods
    // =========================================================================

    /**
     * Make sure that the HTML email template exists at the specified path,
     * and that the text template exists if it was provided.
     *
     * @return bool
     */
    private function _ensureTemplatesExist(): bool
    {
        if ( ! $this->_ensureTemplateExists($this->_htmlEmailTemplate))
        {
            return false;
        }

        if ($this->_textEmailTemplate)
        {
            $this->_ensureTemplateExists($this->_textEmailTemplate);

            return false;
        }

        return true;
    }

    /**
     * Make sure the template exists at the supplied path!
     *
     * @param $path
     * @return bool
     */
    private function _ensureTemplateExists($path): bool
    {
        if ( ! Craft::$app->getView()->doesTemplateExist($path))
        {
            /**
             * A template was specified that doesn't exist!
             */
            Craft::error(sprintf(
                'Specified email template `%s` does not exist.',
                $path
            ), 'snipcart');

            return false;
        }

        return true;
    }

}