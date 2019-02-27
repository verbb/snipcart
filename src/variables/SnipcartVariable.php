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
use workingconcept\snipcart\Snipcart;
use Craft;
use craft\helpers\Template as TemplateHelper;

class SnipcartVariable
{
    // Public Methods
    // =========================================================================

    /**
     * Returns Snipcart public API key.
     *
     * @return string
     */
    public function publicApiKey(): string
    {
        return Snipcart::$plugin->getSettings()->publicApiKey;
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
     * @param string $text
     *
     * @return \Twig_Markup
     *
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
     * Get the main Snipcart JavaScript snippet, optionally including jQuery
     * and Snipcart's cart stylesheet.
     *
     * @param bool   $includejQuery
     * @param string $onload
     * @param bool   $includeStyles
     *
     * @return \Twig_Markup
     * @throws \Twig_Error_Loader
     * @throws \yii\base\Exception
     */
    public function cartSnippet($includejQuery = true, $onload = '', $includeStyles = false): \Twig_Markup
    {
        return $this->_renderTemplate(
            'snipcart/front-end/cart-js',
            [
                'settings'      => Snipcart::$plugin->getSettings(),
                'includejQuery' => $includejQuery,
                'includeStyles' => $includeStyles,
                'onload'        => $onload
            ]
        );
    }


    // Private Methods
    // =========================================================================

    /**
     * Renders an internal (plugin) Twig template.
     *
     * @param $template
     * @param array $data
     *
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
