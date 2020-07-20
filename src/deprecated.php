<?php

/**
 * This file maintains backward compatibility for classes re-namespaced in 1.4.0.
 */

namespace workingconcept\snipcart\models {
    \class_alias(snipcart\Dimensions::class, Dimensions::class);
    \class_alias(snipcart\Item::class, Item::class);
    \class_alias(snipcart\Address::class, Address::class);
    \class_alias(snipcart\OrderEvent::class, OrderEvent::class);
    \class_alias(snipcart\DigitalGood::class, DigitalGood::class);
    \class_alias(snipcart\Category::class, Category::class);
    \class_alias(snipcart\Order::class, Order::class);
    \class_alias(snipcart\Notification::class, Notification::class);
    \class_alias(snipcart\Plan::class, Plan::class);
    \class_alias(snipcart\Product::class, Product::class);
    \class_alias(snipcart\Domain::class, Domain::class);
    \class_alias(snipcart\Discount::class, Discount::class);
    \class_alias(snipcart\Customer::class, Customer::class);
    \class_alias(snipcart\AbandonedCart::class, AbandonedCart::class);
    \class_alias(snipcart\SubscriptionEvent::class, SubscriptionEvent::class);
    \class_alias(snipcart\Tax::class, Tax::class);
    \class_alias(snipcart\ShippingEvent::class, ShippingEvent::class);
    \class_alias(snipcart\PaymentSchedule::class, PaymentSchedule::class);
    \class_alias(snipcart\CustomField::class, CustomField::class);
    \class_alias(snipcart\ShippingRate::class, ShippingRate::class);
    \class_alias(snipcart\ProductVariant::class, ProductVariant::class);
    \class_alias(snipcart\TaxesEvent::class, TaxesEvent::class);
    \class_alias(snipcart\UserSession::class, UserSession::class);
    \class_alias(snipcart\CustomerStatistics::class, CustomerStatistics::class);
    \class_alias(snipcart\Refund::class, Refund::class);
    \class_alias(snipcart\ShippingMethod::class, ShippingMethod::class);
    \class_alias(snipcart\Subscription::class, Subscription::class);
    \class_alias(snipcart\Package::class, Package::class);

    if (! \class_exists(Dimensions::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\Dimensions instead. */
        class Dimensions extends snipcart\Dimensions {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(Item::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\Item instead. */
        class Item extends snipcart\Item {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(Address::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\Address instead. */
        class Address extends snipcart\Address {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(OrderEvent::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\OrderEvent instead. */
        class OrderEvent extends snipcart\OrderEvent {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(DigitalGood::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\DigitalGood instead. */
        class DigitalGood extends snipcart\DigitalGood {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(Category::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\Category instead. */
        class Category extends snipcart\Category {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(Order::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\Order instead. */
        class Order extends snipcart\Order {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(Notification::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\Notification instead. */
        class Notification extends snipcart\Notification {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(Plan::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\Plan instead. */
        class Plan extends snipcart\Plan {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(Product::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\Product instead. */
        class Product extends snipcart\Product {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(Domain::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\Domain instead. */
        class Domain extends snipcart\Domain {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(Discount::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\Discount instead. */
        class Discount extends snipcart\Discount {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(Customer::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\Customer instead. */
        class Customer extends snipcart\Customer {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(AbandonedCart::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\AbandonedCart instead. */
        class AbandonedCart extends snipcart\AbandonedCart {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(SubscriptionEvent::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\SubscriptionEvent instead. */
        class SubscriptionEvent extends snipcart\SubscriptionEvent {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(Tax::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\Tax instead. */
        class Tax extends snipcart\Tax {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(ShippingEvent::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\ShippingEvent instead. */
        class ShippingEvent extends snipcart\ShippingEvent {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(PaymentSchedule::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\PaymentSchedule instead. */
        class PaymentSchedule extends snipcart\PaymentSchedule {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(CustomField::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\CustomField instead. */
        class CustomField extends snipcart\CustomField {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(ShippingRate::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\ShippingRate instead. */
        class ShippingRate extends snipcart\ShippingRate {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(ProductVariant::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\ProductVariant instead. */
        class ProductVariant extends snipcart\ProductVariant {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(TaxesEvent::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\TaxesEvent instead. */
        class TaxesEvent extends snipcart\TaxesEvent {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(UserSession::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\UserSession instead. */
        class UserSession extends snipcart\UserSession {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(CustomerStatistics::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\CustomerStatistics instead. */
        class CustomerStatistics extends snipcart\CustomerStatistics {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(Refund::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\Refund instead. */
        class Refund extends snipcart\Refund {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(ShippingMethod::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\ShippingMethod instead. */
        class ShippingMethod extends snipcart\ShippingMethod {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(Subscription::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\Subscription instead. */
        class Subscription extends snipcart\Subscription {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(Package::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\Package instead. */
        class Package extends snipcart\Package {} // @codingStandardsIgnoreLine
    }
}

namespace workingconcept\snipcart\providers {
    \class_alias(shipstation\ShipStation::class, ShipStation::class);

    if (! \class_exists(ShipStation::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\providers\shipstation\ShipStation instead. */
        class ShipStation extends shipstation\ShipStation {} // @codingStandardsIgnoreLine
    }
}
