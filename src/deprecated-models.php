<?php

namespace workingconcept\snipcart\models;

/**
 * This file provides class aliases to maintain backward compatibility for
 * classes re-namespaced in 1.4.0.
 *
 * It also provides deprecation notices for IDEs.
 */

\class_alias(
    snipcart\Dimensions::class,
    Dimensions::class
);

if (! \class_exists(Dimensions::class)) {
    /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\Dimensions instead. */
    class Dimensions {} // @codingStandardsIgnoreLine
}


\class_alias(
    snipcart\Item::class,
    Item::class
);

if (! \class_exists(Item::class)) {
    /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\Item instead. */
    class Item {} // @codingStandardsIgnoreLine
}


\class_alias(
    snipcart\Address::class,
    Address::class
);

if (! \class_exists(Address::class)) {
    /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\Address instead. */
    class Address {} // @codingStandardsIgnoreLine
}


\class_alias(
    snipcart\OrderEvent::class,
    OrderEvent::class
);

if (! \class_exists(OrderEvent::class)) {
    /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\OrderEvent instead. */
    class OrderEvent {} // @codingStandardsIgnoreLine
}


\class_alias(
    snipcart\DigitalGood::class,
    DigitalGood::class
);

if (! \class_exists(DigitalGood::class)) {
    /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\DigitalGood instead. */
    class DigitalGood {} // @codingStandardsIgnoreLine
}


\class_alias(
    snipcart\Category::class,
    Category::class
);

if (! \class_exists(Category::class)) {
    /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\Category instead. */
    class Category {} // @codingStandardsIgnoreLine
}


\class_alias(
    snipcart\Order::class,
    Order::class
);

if (! \class_exists(Order::class)) {
    /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\Order instead. */
    class Order {} // @codingStandardsIgnoreLine
}


\class_alias(
    snipcart\Notification::class,
    Notification::class
);

if (! \class_exists(Notification::class)) {
    /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\Notification instead. */
    class Notification {} // @codingStandardsIgnoreLine
}


\class_alias(
    snipcart\Plan::class,
    Plan::class
);

if (! \class_exists(Plan::class)) {
    /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\Plan instead. */
    class Plan {} // @codingStandardsIgnoreLine
}


\class_alias(
    snipcart\Product::class,
    Product::class
);

if (! \class_exists(Product::class)) {
    /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\Product instead. */
    class Product {} // @codingStandardsIgnoreLine
}


\class_alias(
    snipcart\Domain::class,
    Domain::class
);

if (! \class_exists(Domain::class)) {
    /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\Domain instead. */
    class Domain {} // @codingStandardsIgnoreLine
}


\class_alias(
    snipcart\Discount::class,
    Discount::class
);

if (! \class_exists(Discount::class)) {
    /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\Discount instead. */
    class Discount {} // @codingStandardsIgnoreLine
}


\class_alias(
    snipcart\Customer::class,
    Customer::class
);

if (! \class_exists(Customer::class)) {
    /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\Customer instead. */
    class Customer {} // @codingStandardsIgnoreLine
}


\class_alias(
    snipcart\AbandonedCart::class,
    AbandonedCart::class
);

if (! \class_exists(AbandonedCart::class)) {
    /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\AbandonedCart instead. */
    class AbandonedCart {} // @codingStandardsIgnoreLine
}


\class_alias(
    snipcart\SubscriptionEvent::class,
    SubscriptionEvent::class
);

if (! \class_exists(SubscriptionEvent::class)) {
    /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\SubscriptionEvent instead. */
    class SubscriptionEvent {} // @codingStandardsIgnoreLine
}


\class_alias(
    snipcart\Tax::class,
    Tax::class
);

if (! \class_exists(Tax::class)) {
    /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\Tax instead. */
    class Tax {} // @codingStandardsIgnoreLine
}


\class_alias(
    snipcart\ShippingEvent::class,
    ShippingEvent::class
);

if (! \class_exists(ShippingEvent::class)) {
    /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\ShippingEvent instead. */
    class ShippingEvent {} // @codingStandardsIgnoreLine
}


\class_alias(
    snipcart\PaymentSchedule::class,
    PaymentSchedule::class
);

if (! \class_exists(PaymentSchedule::class)) {
    /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\PaymentSchedule instead. */
    class PaymentSchedule {} // @codingStandardsIgnoreLine
}


\class_alias(
    snipcart\CustomField::class,
    CustomField::class
);

if (! \class_exists(CustomField::class)) {
    /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\CustomField instead. */
    class CustomField {} // @codingStandardsIgnoreLine
}


\class_alias(
    snipcart\ShippingRate::class,
    ShippingRate::class
);

if (! \class_exists(ShippingRate::class)) {
    /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\ShippingRate instead. */
    class ShippingRate {} // @codingStandardsIgnoreLine
}


\class_alias(
    snipcart\ProductVariant::class,
    ProductVariant::class
);

if (! \class_exists(ProductVariant::class)) {
    /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\ProductVariant instead. */
    class ProductVariant {} // @codingStandardsIgnoreLine
}


\class_alias(
    snipcart\TaxesEvent::class,
    TaxesEvent::class
);

if (! \class_exists(TaxesEvent::class)) {
    /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\TaxesEvent instead. */
    class TaxesEvent {} // @codingStandardsIgnoreLine
}


\class_alias(
    snipcart\UserSession::class,
    UserSession::class
);

if (! \class_exists(UserSession::class)) {
    /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\UserSession instead. */
    class UserSession {} // @codingStandardsIgnoreLine
}


\class_alias(
    snipcart\CustomerStatistics::class,
    CustomerStatistics::class
);

if (! \class_exists(CustomerStatistics::class)) {
    /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\CustomerStatistics instead. */
    class CustomerStatistics {} // @codingStandardsIgnoreLine
}


\class_alias(
    snipcart\Refund::class,
    Refund::class
);

if (! \class_exists(Refund::class)) {
    /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\Refund instead. */
    class Refund {} // @codingStandardsIgnoreLine
}


\class_alias(
    snipcart\ShippingMethod::class,
    ShippingMethod::class
);

if (! \class_exists(ShippingMethod::class)) {
    /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\ShippingMethod instead. */
    class ShippingMethod {} // @codingStandardsIgnoreLine
}


\class_alias(
    snipcart\Subscription::class,
    Subscription::class
);

if (! \class_exists(Subscription::class)) {
    /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\Subscription instead. */
    class Subscription {} // @codingStandardsIgnoreLine
}


\class_alias(
    snipcart\Package::class,
    Package::class
);

if (! \class_exists(Package::class)) {
    /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\Package instead. */
    class Package {} // @codingStandardsIgnoreLine
}
