<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\widgets;

use workingconcept\snipcart\assetbundles\OrdersWidgetAsset;
use workingconcept\snipcart\Snipcart;
use Craft;
use craft\base\Widget;

/**
 * Orders Widget
 */
class Orders extends Widget
{
    /**
     * Disallow multiple widget instances.
     *
     * @return bool
     */
    protected static function allowMultipleInstances(): bool
    {
        return false;
    }

    /**
     * Returns the translated widget display name.
     *
     * @return string
     */
    public static function displayName(): string
    {
        return Craft::t('snipcart', 'Snipcart Orders');
    }

    /**
     * Returns the widget's icon path.
     *
     * @return string
     */
    public static function iconPath()
    {
        //return Craft::getAlias("@workingconcept/snipcart/assetbundles/dist/img/orders-icon.svg");
    }

    /**
     * Sets the maximum column span to 1.
     *
     * @return int
     */
    public static function maxColspan()
    {
        return 1;
    }

    /**
     * Returns the translated widget title.
     *
     * @return string
     */
    public function getTitle(): string {
        return Craft::t('snipcart', 'Snipcart Orders');
    }

    /**
     * Returns the widget body HTML.
     *
     * @return false|string
     * @throws \RuntimeException
     * @throws \Twig_Error_Loader
     * @throws \yii\base\Exception
     */

    public function getBodyHtml()
    {
        //Craft::$app->getView()->registerAssetBundle(OrdersWidgetAsset::class);

        return Craft::$app->getView()->renderTemplate(
            'snipcart/widgets/orders',
            [
                'settings' => Snipcart::$plugin->getSettings()
            ]
        );
    }
}
