<?php

/**
 * This file provides class aliases to maintain backward compatibility for
 * classes re-namespaced in 1.4.0.
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
}

namespace workingconcept\snipcart\providers {
    \class_alias(shipstation\ShipStation::class, ShipStation::class);
}
