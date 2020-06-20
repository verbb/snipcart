<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\assetbundles;

use craft\web\AssetBundle;
use yii\web\JqueryAsset;
use craft\web\assets\cp\CpAsset;

/**
 * @author    Working Concept
 * @package   Snipcart
 * @since     1.0.0
 */
class OverviewAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = '@workingconcept/snipcart/assetbundles/dist';
        $this->depends = [
            SnipcartAsset::class,
            ChartAsset::class,
            CpAsset::class,
            JqueryAsset::class
        ];
        $this->js = ['js/overview.js'];
        $this->css = ['css/snipcart.css'];

        parent::init();
    }
}
