<?php
namespace verbb\snipcart\assetbundles;

use craft\web\AssetBundle;

class ProductDetailsFieldAsset extends AssetBundle
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
            'js/field-product-details.js',
        ];

        $this->css = [
            'css/field-product-details.css',
        ];

        parent::init();
    }
}
