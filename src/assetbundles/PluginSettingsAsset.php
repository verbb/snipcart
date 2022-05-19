<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://fostercommerce.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\assetbundles;

use craft\web\AssetBundle;

/**
 * @author    Working Concept
 * @package   Snipcart
 * @since     1.0.0
 */
class PluginSettingsAsset extends AssetBundle
{
    public function init(): void
    {
        $this->sourcePath = '@fostercommerce/snipcart/assetbundles/dist';
        $this->depends = [SnipcartAsset::class];
        $this->js = ['js/settings-plugin.js'];
        $this->css = ['css/settings-plugin.css'];

        parent::init();
    }
}
