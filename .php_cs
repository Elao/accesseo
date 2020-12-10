<?php

$finder = PhpCsFixer\Finder::create()
    ->in([__DIR__])
    ->exclude('vendor')
;

return (new PhpCsFixer\Config())
    ->setUsingCache(true)
    ->setRiskyAllowed(true)
    ->setFinder($finder)
    ->setRules([
        '@Symfony' => true,
        'declare_strict_types' => true,
        'php_unit_namespaced' => true,
        'psr0' => false,
        'phpdoc_summary' => false,
        'phpdoc_annotation_without_dot' => false,
        'phpdoc_order' => true,
        'array_syntax' => ['syntax' => 'short'],
        'ordered_imports' => true,
        'simplified_null_return' => false,
        'yoda_style' => null,
        'no_superfluous_phpdoc_tags' => true,
        'native_function_invocation' => ['include' => ['@compiler_optimized']],
        'void_return' => true,
        'single_line_throw' => false,
    ])
;
