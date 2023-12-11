<?php
namespace verbb\snipcart\assetbundles;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    Working Concept
 * @package   Snipcart
 * @since     1.0.0
 */
class ChartAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = '@verbb/snipcart/assetbundles/dist';
        $this->depends = [CpAsset::class];
        $this->js = ['js/charts.js'];
        $this->css = ['css/charts.css'];

        parent::init();
    }
}
