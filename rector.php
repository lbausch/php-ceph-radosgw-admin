<?php

declare(strict_types=1);

use Rector\Php74\Rector\Property\TypedPropertyRector;
use Rector\Set\ValueObject\SetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // get parameters
    $parameters = $containerConfigurator->parameters();

    $setLists = [
        SetList::CODE_QUALITY,
        SetList::CODING_STYLE_ADVANCED,
        SetList::CODING_STYLE,
        SetList::DEAD_CODE,
        SetList::EARLY_RETURN,
        SetList::NAMING,
        SetList::ORDER,
        SetList::PHP_74,
        SetList::PHP_80,
        SetList::PHP_81,
        SetList::PRIVATIZATION,
        SetList::PSR_4,
        SetList::TYPE_DECLARATION_STRICT,
        SetList::TYPE_DECLARATION,
        SetList::UNWRAP_COMPAT,
    ];

    // Define what rule sets will be applied
    foreach ($setLists as $setList) {
        $containerConfigurator->import($setList);
    }

    // get services (needed for register a single rule)
    // $services = $containerConfigurator->services();

    // register a single rule
    // $services->set(TypedPropertyRector::class);
};
