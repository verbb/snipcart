<?php
namespace verbb\snipcart\assetbundles;

use craft\web\AssetBundle;

class PluginSettingsAsset extends AssetBundle
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
            'js/settings-plugin.js',
        ];

        $this->css = [
            'css/settings-plugin.css',
        ];

        parent::init();
    }
}
