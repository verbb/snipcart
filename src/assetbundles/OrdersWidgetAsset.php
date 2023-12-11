<?php
namespace verbb\snipcart\assetbundles;

use craft\web\AssetBundle;

class OrdersWidgetAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    public function init(): void
    {
        $this->sourcePath = '@verbb/snipcart/resources/dist';

        $this->depends = [
            ChartAsset::class,
        ];

        $this->js = [
            'js/OrdersWidget.js',
        ];

        parent::init();
    }
}
