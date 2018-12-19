<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\assetbundles\snipcart;

/**
 * @author    Working Concept
 * @package   Snipcart
 * @since     1.0.0
 */
class OrdersWidgetAsset extends \craft\web\AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@workingconcept/snipcart/assetbundles/dist";

        $this->depends = [];
        $this->js = [];
        $this->css = [];

        parent::init();
    }
}
