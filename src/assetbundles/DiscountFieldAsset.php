<?php
namespace verbb\snipcart\assetbundles;

use craft\web\AssetBundle;

class DiscountFieldAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    public function init(): void
    {
        $this->sourcePath = '@verbb/snipcart/resources/dist';

        $this->depends = [
            SnipcartAsset::class,
        ];

        $this->js = [
            'js/settings-discount.js',
        ];

        parent::init();
    }
}
