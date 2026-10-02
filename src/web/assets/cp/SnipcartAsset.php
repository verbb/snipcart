<?php
namespace verbb\snipcart\web\assets\cp;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

use verbb\base\web\assets\cp\CpAsset as VerbbCpAsset;

class SnipcartAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    public function init(): void
    {
        $this->sourcePath = '@verbb/snipcart/web/assets/cp/dist';

        $this->depends = [
            VerbbCpAsset::class,
            CpAsset::class,
        ];

        $this->js = [
            'js/vendors.js',
            'js/snipcart.js',
        ];

        $this->css = [
            'css/snipcart.css',
        ];

        parent::init();
    }
}
