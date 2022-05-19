<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://fostercommerce.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace fostercommerce\snipcart\widgets;

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use Craft;
use craft\base\Widget;
use fostercommerce\snipcart\assetbundles\OrdersWidgetAsset;
use fostercommerce\snipcart\Snipcart;

/**
 * Orders Widget
 */
class Orders extends Widget
{
    /**
     * @var string Type of order data to be displayed.
     */
    public string $chartType = 'itemsSold';

    /**
     * @var string Range of time for which data should be summarized.
     */
    public string $chartRange = 'weekly';

    public static function displayName(): string
    {
        return Craft::t('snipcart', 'Snipcart Orders');
    }

    public static function iconPath(): string
    {
        return Craft::getAlias('@fostercommerce/snipcart/assetbundles/dist/img/orders-icon.svg');
    }

    public static function maxColspan(): int
    {
        return 3;
    }

    public function getTitle(): string
    {
        $rangeName = $this->getChartRangeOptions()[$this->chartRange];
        $typeName = $this->getChartTypeOptions()[$this->chartType];

        return Craft::t('snipcart', sprintf(
            'Snipcart %s %s',
            $rangeName,
            $typeName
        ));
    }

    public function rules(): array
    {
        $rules = parent::rules();

        $rules[] = [['chartType', 'chartRange'], 'required'];
        $rules[] = [['chartType', 'chartRange'], 'string'];
        $rules[] = [
            ['chartType'],
            'in',
            'range' => array_keys($this->getChartTypeOptions()),
        ];
        $rules[] = [
            ['chartRange'],
            'in',
            'range' => array_keys($this->getChartRangeOptions()),
        ];

        return $rules;
    }

    /**
     * Returns the widget body HTML.
     *
     * @return false|string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function getBodyHtml(): ?string
    {
        $view = Craft::$app->getView();

        $view->registerAssetBundle(OrdersWidgetAsset::class);
        $view->registerJs(sprintf(
            'new Craft.OrdersWidget(%d);',
            $this->id
        ));

        return Craft::$app->getView()->renderTemplate(
            'snipcart/widgets/orders/orders',
            [
                'widget' => $this,
                'settings' => Snipcart::$plugin->getSettings(),
            ]
        );
    }

    public function getSettingsHtml(): ?string
    {
        return Craft::$app->getView()->renderTemplate(
            'snipcart/widgets/orders/settings',
            [
                'widget' => $this,
            ]
        );
    }

    /**
     * Get a key-value array representing options for the type of data to be charted.
     */
    public function getChartTypeOptions(): array
    {
        return [
            'totalSales' => 'Sales',
            'numberOfOrders' => 'Orders',
        ];
    }

    /**
     * Get a key-value array representing options for the chart's time range.
     */
    public function getChartRangeOptions(): array
    {
        return [
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
        ];
    }
}
