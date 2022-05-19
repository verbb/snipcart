<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://fostercommerce.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\assetbundles;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    Working Concept
 * @package   Snipcart
 * @since     1.0.0
 */
class ChartAsset extends AssetBundle
{
    public function init(): void
    {
        $this->sourcePath = '@fostercommerce/snipcart/assetbundles/dist';
        $this->depends = [CpAsset::class];
        $this->js = ['js/charts.js'];
        $this->css = ['css/charts.css'];

        parent::init();
    }
}
