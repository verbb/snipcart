<?php
declare(strict_types = 1);

use craft\rector\SetList as CraftSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Config\RectorConfig;

return static function(RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/src',
        __FILE__,
    ]);

    $rectorConfig->importNames(true, false);
    $rectorConfig->importShortClasses(false);

    $rectorConfig->sets([
        SetList::PHP_80,
        SetList::PHP_74,
        SetList::PHP_73,
        SetList::PHP_72,
        SetList::PHP_71,
        SetList::PHP_70,
        SetList::PHP_56,
        SetList::PHP_55,
        SetList::PHP_54,
        SetList::PHP_53,
        SetList::PHP_52,
        SetList::CODE_QUALITY,
        SetList::CODING_STYLE,
//        SetList::DEAD_CODE,
        SetList::STRICT_BOOLEANS,
        SetList::NAMING,
        SetList::TYPE_DECLARATION,
        SetList::EARLY_RETURN,
//        SetList::INSTANCEOF,
        CraftSetList::CRAFT_CMS_40,
        CraftSetList::CRAFT_COMMERCE_40,
    ]);

};
