<?php
namespace verbb\snipcart\web\assets\cp;

use craft\web\AssetBundle;

class ProductDetailsFieldAsset extends AssetBundle
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
            'js/field-product-details.js',
        ];

        $this->css = [
            'css/field-product-details.css',
        ];

        parent::init();
    }
}
