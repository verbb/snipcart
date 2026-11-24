<?php
namespace verbb\snipcart\web\assets\cp;

use craft\web\AssetBundle;

class PluginKitAsset extends AssetBundle
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
            'js/kit.js',
        ];

        $this->css = [
            'css/kit.css',
        ];

        parent::init();
    }
}
