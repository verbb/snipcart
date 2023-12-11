<?php
namespace verbb\snipcart\services;

use verbb\snipcart\Snipcart;

use Craft;
use craft\base\Component;
use craft\mail\Message;

use yii\base\Exception;

use Pelago\Emogrifier\CssInliner;

class Notifications extends Component
{
    // Properties
    // =========================================================================

    private string $htmlEmailTemplate;
    private string $textEmailTemplate;
    private bool $emailTemplatesAreFrontEnd;
    private string $slackWebhook;
    private mixed $notificationVars;
    private array $errors = [];


    // Public Methods
    // =========================================================================

    public function setNotificationVars(mixed $data): void
    {
        $this->notificationVars = $data;
    }

    public function getNotificationVars(): mixed
    {
        return $this->notificationVars;
    }

    public function setErrors(array $errors): void
    {
        $this->errors = $errors;
    }

    public function setEmailTemplate(string $htmlTemplate, string $textTemplate = null, bool $frontend = false): void
    {
        $this->htmlEmailTemplate = $htmlTemplate;
        $this->textEmailTemplate = $textTemplate;
        $this->emailTemplatesAreFrontEnd = $frontend;
    }

    public function setSlackWebhook(string $url): void
    {
        $this->slackWebhook = $url;
    }

    public function sendEmail(array $to, string $subject): bool
    {
        $model = Snipcart::$plugin->getSettings();

        // Bail if we’re in test mode and don’t want to send emails.
        if ($model->testMode && ! $model->sendTestModeEmail) {
            // pretend it went well
            return true;
        }

        $errors = [];
        $view = Craft::$app->getView();

        $emailSettings = Craft::$app->getProjectConfig()->get('email');

        // Switch template mode only if we need to rely on our own template.
        if ($this->emailTemplatesAreFrontEnd === false) {
            $originalTemplateMode = $view->getTemplateMode();

            $view->setTemplateMode($view::TEMPLATE_MODE_CP);
        }

        if (!$this->ensureTemplatesExist()) {
            $this->setErrors(['Email template(s) are missing.']);
            return false;
        }

        // render the HTML message
        $messageHtml = $view->renderPageTemplate($this->htmlEmailTemplate, $this->getNotificationVars());

        // inline the message’s styles so they're more likely to be applied
        $mergedHtml = CssInliner::fromHtml($messageHtml)->inlineCss()->render();

        $messageText = '';

        if ($this->textEmailTemplate !== '' && $this->textEmailTemplate !== '0') {
            $messageText = $view->renderPageTemplate($this->textEmailTemplate, $this->getNotificationVars());
        }

        foreach ($to as $address) {
            $message = new Message();

            $message->setFrom([
                $emailSettings['fromEmail'] => $emailSettings['fromName'],
            ]);

            $message->setTo($address);
            $message->setSubject($subject);
            $message->setHtmlBody($mergedHtml);

            if ($messageText) {
                $message->setTextBody($messageText);
            }

            if (!Craft::$app->getMailer()->send($message)) {
                $problem = "Notification failed to send to $address!";
                Snipcart::error($problem);

                $errors[] = $problem;
            }
        }

        if ($this->emailTemplatesAreFrontEnd === false && isset($originalTemplateMode)) {
            $view->setTemplateMode($originalTemplateMode);
        }

        $this->setErrors($errors);

        return $errors === [];
    }


    // Private Methods
    // =========================================================================

    private function ensureTemplatesExist(): bool
    {
        if (!$this->ensureTemplateExists($this->htmlEmailTemplate)) {
            return false;
        }

        if ($this->textEmailTemplate !== '' && $this->textEmailTemplate !== '0') {
            $this->ensureTemplateExists($this->textEmailTemplate);

            return false;
        }

        return true;
    }

    private function ensureTemplateExists($path): bool
    {
        if (!Craft::$app->getView()->doesTemplateExist($path)) {
            Snipcart::error('Specified email template `{path}` does not exist.', [
                'path' => $path,
            ]);
            
            return false;
        }

        return true;
    }
}
