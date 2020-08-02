<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\variables;

use workingconcept\snipcart\fields\ProductDetails;
use workingconcept\snipcart\helpers\FieldHelper;
use workingconcept\snipcart\helpers\FormatHelper;
use workingconcept\snipcart\helpers\VersionHelper;
use workingconcept\snipcart\models\snipcart\Customer;
use workingconcept\snipcart\models\snipcart\Order;
use workingconcept\snipcart\models\snipcart\Subscription;
use workingconcept\snipcart\Snipcart;
use Craft;
use craft\helpers\Template as TemplateHelper;
use yii\base\InvalidConfigException;

class SnipcartVariable
{
    /**
     * Returns Snipcart public API key.
     *
     * @return string
     */
    public function publicApiKey(): string
    {
        return Snipcart::$plugin->getSettings()->publicKey();
    }

    /**
     * Returns the default currency.
     *
     * @return string
     */
    public function defaultCurrency(): string
    {
        return Snipcart::$plugin->getSettings()->getDefaultCurrency();
    }

    /**
     * Returns the default currency symbol.
     *
     * @return string
     */
    public function defaultCurrencySymbol(): string
    {
        return Snipcart::$plugin->getSettings()->getDefaultCurrencySymbol();
    }

    /**
     * Returns formatted currency string.
     *
     * @param mixed  $value        The value to be formatted.
     * @param string $currencyType Optional string representing desired currency
     *                             to be explicitly set.
     *
     * @return string
     * @throws InvalidConfigException if no currency is given and [[currencyCode]] is not defined.
     */
    public function formatCurrency($value, $currencyType = null): string
    {
        return FormatHelper::formatCurrency($value, $currencyType);
    }

    /**
     * Returns a compact, general, relative, human-readable string representing
     * the age of the provided DateTime.
     *
     * @param  \DateTime  $date
     *
     * @return string
     */
    public function tinyDateInterval(\DateTime $date): string
    {
        return FormatHelper::tinyDateInterval($date);
    }

    /**
     * Returns a Snipcart customer by ID.
     *
     * @param string $customerId
     * @return Customer|null
     * @throws \Exception if API key is missing.
     */
    public function getCustomer($customerId)
    {
        return Snipcart::$plugin->customers->getCustomer($customerId);
    }

    /**
     * Returns a Snipcart order by ID.
     *
     * @param string $orderId
     * @return Order|null
     * @throws \Exception if API key is missing.
     */
    public function getOrder($orderId)
    {
        return Snipcart::$plugin->orders->getOrder($orderId);
    }

    /**
     * Returns a Snipcart subscription by ID.
     *
     * @param string $subscriptionId
     * @return Subscription|null
     * @throws \Exception if API key is missing.
     */
    public function getSubscription($subscriptionId)
    {
        return Snipcart::$plugin->subscriptions->getSubscription($subscriptionId);
    }

    /**
     * Returns product info for the provided Element regardless of what the
     * field handle might be.
     *
     * @param \craft\base\Element $element
     * @return ProductDetails|null
     */
    public function getProductInfo($element)
    {
        return FieldHelper::getProductInfo($element);
    }

    /**
     * Gets a cart anchor with a count.
     *
     * @param  string  $text       Button's inner text. Defaults to `Shopping Cart`.
     * @param  bool    $showCount  `false` to remove dynamic item count.
     *
     * @return \Twig\Markup
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \yii\base\Exception
     */
    public function cartLink($text = null, $showCount = true): \Twig\Markup
    {
        return $this->renderTemplate(
            'snipcart/front-end/cart-link',
            [
                'text' => $text,
                'showCount' => $showCount
            ]
        );
    }

    /**
     * Get the main Snipcart JavaScript snippet, optionally including jQuery
     * and Snipcart's cart stylesheet.
     *
     * @param  bool    $includejQuery
     * @param  string  $onload
     * @param  bool    $includeStyles
     *
     * @return \Twig\Markup
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \yii\base\Exception
     */
    public function cartSnippet($includejQuery = true, $onload = '', $includeStyles = true): \Twig\Markup
    {
        $settings = Snipcart::$plugin->getSettings();
        $publicApiKey = $settings->publicKey();

        if (VersionHelper::isCraft31()) {
            $publicApiKey = Craft::parseEnv($publicApiKey);
        }

        return $this->renderTemplate(
            'snipcart/front-end/cart-js',
            [
                'settings'      => $settings,
                'includejQuery' => $includejQuery,
                'includeStyles' => $includeStyles,
                'publicApiKey'  => $publicApiKey,
                'onload'        => $onload
            ]
        );
    }


    /**
     * Renders an internal (plugin) Twig template.
     *
     * @param         $template
     * @param  array  $data
     *
     * @return \Twig\Markup
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \yii\base\Exception
     */
    private function renderTemplate($template, $data = []): \Twig\Markup
    {
        $view         = Craft::$app->getView();
        $templateMode = $view->getTemplateMode();

        // use CP mode
        $view->setTemplateMode($view::TEMPLATE_MODE_CP);

        // render the thing
        $html = $view->renderTemplate($template, $data);

        // put it back how we found it
        $view->setTemplateMode($templateMode);

        return TemplateHelper::raw($html);
    }

}
