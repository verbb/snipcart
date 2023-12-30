<?php
namespace verbb\snipcart\widgets;

use verbb\snipcart\Snipcart;
use verbb\snipcart\assetbundles\OrdersWidgetAsset;

use Craft;
use craft\base\Widget;

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

use yii\base\Exception;
use yii\base\InvalidConfigException;

class Orders extends Widget
{
    // Properties
    // =========================================================================

    public string $chartType = 'itemsSold';
    public string $chartRange = 'weekly';


    // Public Methods
    // =========================================================================

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

        return Craft::t('snipcart', 'Snipcart {range} {type}', ['range' => $rangeNames, 'type' => $typeName]);
    }

    public function getBodyHtml(): ?string
    {
        $view = Craft::$app->getView();

        $view->registerAssetBundle(OrdersWidgetAsset::class);
        $view->registerJs("new Craft.OrdersWidget($this->id);");

        return Craft::$app->getView()->renderTemplate('snipcart/widgets/orders/orders', [
            'widget' => $this,
            'settings' => Snipcart::$plugin->getSettings(),
        ]);
    }

    public function getSettingsHtml(): ?string
    {
        return Craft::$app->getView()->renderTemplate('snipcart/widgets/orders/settings', [
            'widget' => $this,
        ]);
    }

    public function getChartTypeOptions(): array
    {
        return [
            'totalSales' => 'Sales',
            'numberOfOrders' => 'Orders',
        ];
    }

    public function getChartRangeOptions(): array
    {
        return [
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
        ];
    }
    

    // Protected Methods
    // =========================================================================

    protected function defineRules(): array
    {
        $rules = parent::defineRules();
        
        $rules[] = [['chartType', 'chartRange'], 'required'];
        $rules[] = [['chartType'], 'in', 'range' => array_keys($this->getChartTypeOptions())];
        $rules[] = [['chartRange'], 'in', 'range' => array_keys($this->getChartRangeOptions())];

        return $rules;
    }
}
