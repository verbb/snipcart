<?php

declare(strict_types=1);

use craft\ecs\SetList as CraftSetList;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function(ECSConfig $ecsConfig): void {
    $ecsConfig->parallel();
    $ecsConfig->paths([
        __DIR__ . '/src',
        __FILE__,
    ]);

    $ecsConfig->sets([
        SetList::ARRAY,
        SetList::COMMENTS,
        // SetList::CONTROL_STRUCTURES,
        SetList::DOCBLOCK,
        SetList::NAMESPACES,
        SetList::PHPUNIT,
        SetList::SPACES,
        SetList::STRICT,
        SetList::CLEAN_CODE,
        SetList::PSR_12,
        CraftSetList::CRAFT_CMS_4,
    ]);

    $ecsConfig->services()->remove(DeclareStrictTypesFixer::class);
};