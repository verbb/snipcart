<?php
namespace verbb\snipcart\assetbundles;

use craft\web\AssetBundle;

/**
 * @author    Working Concept
 * @package   Snipcart
 * @since     1.0.0
 */
class ProductDetailsFieldAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = '@verbb/snipcart/assetbundles/dist';
        $this->depends = [SnipcartAsset::class];
        $this->js = ['js/field-product-details.js'];
        $this->css = ['css/field-product-details.css'];

        parent::init();
    }
}
