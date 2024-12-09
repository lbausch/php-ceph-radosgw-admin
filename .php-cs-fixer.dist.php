<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude('vendor')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        'global_namespace_import' => [
            'import_classes' => true,
        ],
        'no_superfluous_phpdoc_tags' => [
            'allow_mixed' => true,
        ],
    ])
    ->setFinder($finder)
;
