<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://fostercommerce.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\assetbundles;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;
use yii\web\JqueryAsset;

/**
 * @author    Working Concept
 * @package   Snipcart
 * @since     1.0.0
 */
class OrdersWidgetAsset extends AssetBundle
{
    public function init(): void
    {
        $this->sourcePath = '@fostercommerce/snipcart/assetbundles/dist';
        $this->depends = [
            SnipcartAsset::class,
            ChartAsset::class,
            CpAsset::class,
            JqueryAsset::class,
        ];
        $this->js = ['js/OrdersWidget.js'];
        $this->css = [];

        parent::init();
    }
}
