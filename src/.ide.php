<?php

/**
 * This file provides deprecation notices for a PHP IDE without any need to be
 * loaded; it won’t throw deprecation errors during composer updates.
 */

namespace workingconcept\snipcart\models {
    if (! \class_exists(Dimensions::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\Dimensions instead. */
        class Dimensions {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(Item::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\Item instead. */
        class Item {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(Address::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\Address instead. */
        class Address {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(OrderEvent::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\OrderEvent instead. */
        class OrderEvent {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(DigitalGood::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\DigitalGood instead. */
        class DigitalGood {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(Category::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\Category instead. */
        class Category {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(Order::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\Order instead. */
        class Order {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(Notification::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\Notification instead. */
        class Notification {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(Plan::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\Plan instead. */
        class Plan {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(Product::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\Product instead. */
        class Product {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(Domain::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\Domain instead. */
        class Domain {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(Discount::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\Discount instead. */
        class Discount {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(Customer::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\Customer instead. */
        class Customer {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(AbandonedCart::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\AbandonedCart instead. */
        class AbandonedCart {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(SubscriptionEvent::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\SubscriptionEvent instead. */
        class SubscriptionEvent {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(Tax::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\Tax instead. */
        class Tax {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(ShippingEvent::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\ShippingEvent instead. */
        class ShippingEvent {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(PaymentSchedule::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\PaymentSchedule instead. */
        class PaymentSchedule {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(CustomField::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\CustomField instead. */
        class CustomField {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(ShippingRate::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\ShippingRate instead. */
        class ShippingRate {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(ProductVariant::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\ProductVariant instead. */
        class ProductVariant {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(TaxesEvent::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\TaxesEvent instead. */
        class TaxesEvent {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(UserSession::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\UserSession instead. */
        class UserSession {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(CustomerStatistics::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\CustomerStatistics instead. */
        class CustomerStatistics {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(Refund::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\Refund instead. */
        class Refund {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(ShippingMethod::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\ShippingMethod instead. */
        class ShippingMethod {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(Subscription::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\Subscription instead. */
        class Subscription {} // @codingStandardsIgnoreLine
    }

    if (! \class_exists(Package::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\models\snipcart\Package instead. */
        class Package {} // @codingStandardsIgnoreLine
    }
}

namespace workingconcept\snipcart\providers {
    if (! \class_exists(ShipStation::class)) {
        /** @deprecated in 1.4.0. Use workingconcept\snipcart\providers\shipstation\ShipStation instead. */
        class ShipStation {} // @codingStandardsIgnoreLine
    }
}
