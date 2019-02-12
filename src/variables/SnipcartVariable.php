<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\variables;

use workingconcept\snipcart\models\AbandonedCart;
use workingconcept\snipcart\models\Customer;
use workingconcept\snipcart\models\Discount;
use workingconcept\snipcart\models\Order;
use workingconcept\snipcart\models\Subscription;
use workingconcept\snipcart\Snipcart;
use Craft;
use craft\helpers\Template as TemplateHelper;

class SnipcartVariable
{
    // Public Methods
    // =========================================================================

    /**
     * @return string
     */
    public function publicApiKey(): string
    {
        return Snipcart::$plugin->getSettings()->publicApiKey;
    }

    /**
     * @return bool
     */
    public function isLinked(): bool
    {
        return Snipcart::$plugin->api->isLinked;
    }

    /**
     * @return string
     */
    public function defaultCurrencySymbol(): string
    {
        return Snipcart::$plugin->getSettings()->getDefaultCurrencySymbol();
    }

    /**
     * Get a cart anchor with a count.
     *
     * @param string $text
     *
     * @return \Twig_Markup
     * @throws \Twig_Error_Loader
     * @throws \yii\base\Exception
     */
    public function cartLink($text = null): \Twig_Markup
    {
        return $this->_renderTemplate(
            'snipcart/front-end/cart-link',
            [ 'text' => $text ]
        );
    }

    /**
     * Get the main Snipcart JavaScript snippet, optionally including jQuery.
     *
     * @param bool $includejQuery
     * @param string $onload
     *
     * @return \Twig_Markup
     * @throws \Twig_Error_Loader
     * @throws \yii\base\Exception
     */
    public function cartSnippet($includejQuery = true, $onload = ''): \Twig_Markup
    {
        return $this->_renderTemplate(
            'snipcart/front-end/cart-js',
            [
                'settings'      => Snipcart::$plugin->getSettings(),
                'includejQuery' => $includejQuery,
                'onload'        => $onload
            ]
        );
    }

    // Private Methods
    // =========================================================================

    /**
     * @param $template
     * @param array $data
     * @return \Twig_Markup
     * @throws \Twig_Error_Loader
     * @throws \yii\base\Exception
     */
    private function _renderTemplate($template, $data = []): \Twig_Markup
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
