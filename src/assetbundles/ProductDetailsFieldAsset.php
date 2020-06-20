<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\assetbundles;

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
        $this->sourcePath = '@workingconcept/snipcart/assetbundles/dist';
        $this->depends = [SnipcartAsset::class];
        $this->js = ['js/field-product-details.js'];
        $this->css = ['css/field-product-details.css'];

        parent::init();
    }
}
