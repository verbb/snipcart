<?php
namespace verbb\snipcart\web\assets\cp;

use craft\web\AssetBundle;

class DiscountFieldAsset extends AssetBundle
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
            'js/settings-discount.js',
        ];

        parent::init();
    }
}
