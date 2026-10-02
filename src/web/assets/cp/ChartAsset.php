<?php
namespace verbb\snipcart\web\assets\cp;

use craft\web\AssetBundle;

class ChartAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    public function init(): void
    {
        $this->sourcePath = '@verbb/snipcart/web/assets/cp/dist';

        $this->depends = [
            SnipcartAsset::class,
        ];

        $this->js = [
            'js/charts.js',
        ];

        $this->css = [
            'css/charts.css',
        ];

        parent::init();
    }
}
