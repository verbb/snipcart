<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\widgets;

use workingconcept\snipcart\assetbundles\OrdersWidgetAsset;
use workingconcept\snipcart\Snipcart;
use Craft;
use craft\base\Widget;

/**
 * Orders Widget
 */
class Orders extends Widget
{

    // Properties
    // =========================================================================

    /**
     * @var string Type of order data to be displayed.
     */
    public $chartType = 'itemsSold';

    /**
     * @var string Range of time for which data should be summarized.
     */
    public $chartRange = 'weekly';


    // Public Methods
    // =========================================================================

    /**
     * Returns the translated widget display name.
     *
     * @return string
     */
    public static function displayName(): string
    {
        return Craft::t('snipcart', 'Snipcart Orders');
    }

    /**
     * Returns the widget's icon path.
     *
     * @return string|null|boolean
     */
    public static function iconPath()
    {
        return Craft::getAlias('@workingconcept/snipcart/assetbundles/dist/img/orders-icon.svg');
    }

    /**
     * Sets the maximum column span to 1.
     *
     * @return int
     */
    public static function maxColspan(): int
    {
        return 3;
    }

    /**
     * Returns the translated widget title.
     *
     * @return string
     */
    public function getTitle(): string
    {
        $rangeName = $this->getChartRangeOptions()[$this->chartRange];
        $typeName  = $this->getChartTypeOptions()[$this->chartType];

        return Craft::t('snipcart', sprintf('Snipcart %s %s',
            $rangeName,
            $typeName
        ));
    }

   /**
    * @inheritdoc
    */
   public function rules(): array
   {
       $rules = parent::rules();

       $rules[] = [['chartType', 'chartRange'], 'required'];
       $rules[] = [['chartType', 'chartRange'], 'string'];
       $rules[] = [['chartType'], 'in', 'range' => array_keys($this->getChartTypeOptions())];
       $rules[] = [['chartRange'], 'in', 'range' => array_keys($this->getChartRangeOptions())];

       return $rules;
   }

    /**
     * Returns the widget body HTML.
     *
     * @return false|string
     * @throws \RuntimeException
     * @throws \Twig_Error_Loader
     * @throws \yii\base\Exception
     */
    public function getBodyHtml()
    {
        $view = Craft::$app->getView();

        $view->registerAssetBundle(OrdersWidgetAsset::class);
        $view->registerJs(sprintf('new Craft.OrdersWidget(%d);',
            $this->id
        ));

        return Craft::$app->getView()->renderTemplate(
            'snipcart/widgets/orders/orders',
            [
                'widget'   => $this,
                'settings' => Snipcart::$plugin->getSettings()
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml()
    {
        return Craft::$app->getView()->renderTemplate('snipcart/widgets/orders/settings',
            [
                'widget' => $this
            ]
        );
    }

    /**
     * Get a key-value array representing options for the type of data to be charted.
     *
     * @return array
     */
    public function getChartTypeOptions(): array
    {
        return [
            'totalSales'     => 'Sales',
            'numberOfOrders' => 'Orders',
        ];
    }

    /**
     * Get a key-value array representing options for the chart's time range.
     *
     * @return array
     */
    public function getChartRangeOptions(): array
    {
        return [
            'weekly'  => 'Weekly',
            'monthly' => 'Monthly',
        ];
    }

}
