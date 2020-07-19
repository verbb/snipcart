<?php

namespace workingconcept\snipcart\providers;

/**
 * This file provides class aliases to maintain backward compatibility for
 * classes re-namespaced in 1.4.0.
 *
 * It also provides deprecation notices for IDEs.
 */

\class_alias(
    shipstation\ShipStation::class,
    ShipStation::class
);

if (! \class_exists(ShipStation::class)) {
    /** @deprecated in 1.4.0. Use workingconcept\snipcart\providers\shipstation\ShipStation instead. */
    class ShipStation {} // @codingStandardsIgnoreLine
}
