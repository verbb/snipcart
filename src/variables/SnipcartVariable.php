<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://fostercommerce.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\variables;

use craft\base\Element;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\Exception;
use Twig\Markup;
use Craft;
use craft\helpers\Template as TemplateHelper;
use fostercommerce\snipcart\fields\ProductDetails;
use fostercommerce\snipcart\helpers\FieldHelper;
use fostercommerce\snipcart\helpers\FormatHelper;
use fostercommerce\snipcart\helpers\VersionHelper;
use fostercommerce\snipcart\models\snipcart\Customer;
use fostercommerce\snipcart\models\snipcart\Order;
use fostercommerce\snipcart\models\snipcart\Subscription;
use fostercommerce\snipcart\Snipcart;
use yii\base\InvalidConfigException;

class SnipcartVariable
{
    /**
     * Returns Snipcart public API key.
     */
    public function publicApiKey(): string
    {
        return Snipcart::$plugin->getSettings()->publicKey();
    }

    /**
     * Returns the default currency.
     */
    public function defaultCurrency(): string
    {
        return Snipcart::$plugin->getSettings()->getDefaultCurrency();
    }

    /**
     * Returns the default currency symbol.
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
     * @throws InvalidConfigException if no currency is given and [[currencyCode]] is not defined.
     */
    public function formatCurrency(mixed $value, $currencyType = null): string
    {
        return FormatHelper::formatCurrency($value, $currencyType);
    }

    /**
     * Returns a compact, general, relative, human-readable string representing
     * the age of the provided DateTime.
     */
    public function tinyDateInterval(\DateTime $dateTime): string
    {
        return FormatHelper::tinyDateInterval($dateTime);
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
     * @return Order|null
     * @throws \Exception if API key is missing.
     */
    public function getOrder(string $orderId)
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
     * @param Element $element
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
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    public function cartLink($text = null, $showCount = true): Markup
    {
        return $this->renderTemplate(
            'snipcart/front-end/cart-link',
            [
                'text' => $text,
                'showCount' => $showCount,
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
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    public function cartSnippet($includejQuery = true, $onload = '', $includeStyles = true): Markup
    {
        $model = Snipcart::$plugin->getSettings();
        $publicApiKey = $model->publicKey();

        if (VersionHelper::isCraft31()) {
            $publicApiKey = Craft::parseEnv($publicApiKey);
        }

        return $this->renderTemplate(
            'snipcart/front-end/cart-js',
            [
                'settings' => $model,
                'includejQuery' => $includejQuery,
                'includeStyles' => $includeStyles,
                'publicApiKey' => $publicApiKey,
                'onload' => $onload,
            ]
        );
    }

    /**
     * Renders an internal (plugin) Twig template.
     *
     * @param         $template
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    private function renderTemplate(string $template, array $data = []): Markup
    {
        $view = Craft::$app->getView();
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
