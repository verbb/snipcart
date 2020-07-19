<?php

namespace workingconcept\snipcart\providers;

\class_alias(
    shipstation\ShipStation::class,
    ShipStation::class
);

if (! \class_exists(ShipStation::class)) {
    /** @deprecated use workingconcept\snipcart\providers\shipstation\ShipStation */
    class ShipStation {} // @codingStandardsIgnoreLine
}
