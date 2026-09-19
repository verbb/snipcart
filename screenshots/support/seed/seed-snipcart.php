/** Seed a real Product Details field, product entry and dashboard widget for Snipcart captures. */

use craft\elements\Entry;
use craft\elements\User;
use craft\fieldlayoutelements\CustomField;
use craft\helpers\Json;
use craft\models\EntryType;
use craft\models\FieldLayout;
use craft\models\FieldLayoutTab;
use craft\models\Section;
use craft\models\Section_SiteSettings;
use verbb\snipcart\fields\ProductDetails;
use verbb\snipcart\widgets\Orders;

const SCREENSHOT_FIELD_HANDLE = 'productDetails';
const SCREENSHOT_SECTION_HANDLE = 'snipcartProducts';

$fields = Craft::$app->getFields();
$field = $fields->getFieldByHandle(SCREENSHOT_FIELD_HANDLE);

if (!$field instanceof ProductDetails) {
    $field = new ProductDetails(['handle' => SCREENSHOT_FIELD_HANDLE]);
}

$field->name = 'Product details';
$field->instructions = 'Set the price, stock and shipping details used by Snipcart.';
$field->displayInventory = true;
$field->displayShippableSwitch = true;
$field->displayTaxableSwitch = true;
$field->defaultShippable = true;
$field->defaultTaxable = true;
$field->defaultWeight = '0.8';
$field->defaultWeightUnit = 'pounds';
$field->defaultDimensionsUnit = 'centimeters';

if (!$fields->saveField($field)) {
    throw new RuntimeException('Unable to save the Snipcart Product Details field: ' . Json::encode($field->getErrors()));
}

$entries = Craft::$app->getEntries();
$section = $entries->getSectionByHandle(SCREENSHOT_SECTION_HANDLE);
$site = Craft::$app->getSites()->getPrimarySite();

if (!$section) {
    $entryType = new EntryType([
        'name' => 'Product',
        'handle' => 'product',
        'hasTitleField' => true,
    ]);

    if (!$entries->saveEntryType($entryType)) {
        throw new RuntimeException('Unable to save the product entry type: ' . Json::encode($entryType->getErrors()));
    }

    $section = new Section([
        'name' => 'Products',
        'handle' => SCREENSHOT_SECTION_HANDLE,
        'type' => Section::TYPE_CHANNEL,
    ]);
    $section->setEntryTypes([$entryType]);
    $section->setSiteSettings([
        new Section_SiteSettings([
            'siteId' => $site->id,
            'enabledByDefault' => true,
            'hasUrls' => false,
        ]),
    ]);

    if (!$entries->saveSection($section)) {
        throw new RuntimeException('Unable to save the product section: ' . Json::encode($section->getErrors()));
    }

    $section = $entries->getSectionByHandle(SCREENSHOT_SECTION_HANDLE);
}

$entryType = $entries->getEntryTypesBySectionId($section->id)[0] ?? null;
if (!$entryType) {
    throw new RuntimeException('The Snipcart screenshot section has no entry type.');
}

$layout = $entryType->getFieldLayout() ?? new FieldLayout(['type' => Entry::class]);
$tab = $layout->getTabs()[0] ?? new FieldLayoutTab(['name' => 'Content', 'layout' => $layout]);
$elements = array_values(array_filter(
    $tab->getElements(),
    static fn($element) => !($element instanceof CustomField && $element->getField()?->id === $field->id),
));
$elements[] = new CustomField($field);
$tab->setElements($elements);
$layout->setTabs([$tab]);
$entryType->setFieldLayout($layout);

if (!$entries->saveEntryType($entryType)) {
    throw new RuntimeException('Unable to attach the Product Details field: ' . Json::encode($entryType->getErrors()));
}

$entry = Entry::find()->sectionId($section->id)->siteId($site->id)->status(null)->one() ?? new Entry([
    'sectionId' => $section->id,
    'typeId' => $entryType->id,
    'siteId' => $site->id,
    'enabled' => true,
]);
$entry->title = 'Harbour linen throw';
$entry->slug = 'harbour-linen-throw';
$entry->enabled = true;
$entry->setFieldValue($field->handle, [
    'sku' => 'LINEN-THROW-NAVY',
    'inventory' => 48,
    'price' => 118,
    'taxable' => true,
    'shippable' => true,
    'weight' => 0.8,
    'weightUnit' => 'pounds',
    'length' => 45,
    'width' => 32,
    'height' => 8,
    'dimensionsUnit' => 'centimeters',
]);

if (!Craft::$app->getElements()->saveElement($entry)) {
    throw new RuntimeException('Unable to save the Snipcart product entry: ' . Json::encode($entry->getErrors()));
}

$dashboard = Craft::$app->getDashboard();
$admin = User::find()->admin()->one();
if (!$admin) {
    throw new RuntimeException('Unable to find the screenshot administrator for the dashboard widget.');
}
Craft::$app->getUser()->setIdentity($admin);

$hasWidget = false;
foreach ($dashboard->getAllWidgets() as $widget) {
    if ($widget instanceof Orders) {
        $hasWidget = true;
        $dashboard->changeWidgetColspan($widget->id, 2);
        break;
    }
}

if (!$hasWidget) {
    $widget = $dashboard->createWidget([
        'type' => Orders::class,
        'chartType' => 'totalSales',
        'chartRange' => 'weekly',
        'colspan' => 2,
    ]);

    if (!$dashboard->saveWidget($widget)) {
        throw new RuntimeException('Unable to save the Snipcart dashboard widget: ' . Json::encode($widget->getErrors()));
    }

    $dashboard->changeWidgetColspan($widget->id, 2);
}

Craft::$app->getCache()->flush();

$entryEditRoute = parse_url((string)$entry->getCpEditUrl(), PHP_URL_PATH)
    ?: '/admin/entries/' . $section->handle . '/' . $entry->id . '-' . $entry->slug;

echo Json::encode([
    'entryEditRoute' => $entryEditRoute,
    'orderRoute' => '/admin/snipcart/order/order-melbourne-1048',
], JSON_THROW_ON_ERROR);
