<?php
namespace verbb\snipcart\assetbundles;

use craft\web\AssetBundle;

class OverviewAsset extends AssetBundle
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
            'js/overview.js',
        ];

        $this->css = [
            'css/snipcart.css',
        ];

        parent::init();
    }
}
