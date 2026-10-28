<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in([
        __DIR__,
        __DIR__ . '/../simflex-core/src',
    ])
    ->exclude([
        'cache',
        'log',
        'vendor',
    ])
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new Config())
    ->setFinder($finder)
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'binary_operator_spaces' => ['default' => 'single_space'],
        'braces_position' => true,
        'class_attributes_separation' => [
            'elements' => [
                'method' => 'one',
                'trait_import' => 'none',
            ],
        ],
        'control_structure_braces' => true,
        'function_declaration' => [
            'closure_fn_spacing' => 'none',
        ],
        'lowercase_cast' => true,
        'no_blank_lines_after_class_opening' => true,
        'no_extra_blank_lines' => [
            'tokens' => ['extra', 'throw', 'use'],
        ],
        'no_multiple_statements_per_line' => true,
        'no_unused_imports' => true,
        'no_trailing_whitespace' => true,
        'no_whitespace_in_blank_line' => true,
        'single_blank_line_at_eof' => true,
        'single_class_element_per_statement' => true,
        'single_quote' => true,
        'trailing_comma_in_multiline' => true,
        'visibility_required' => [
            'elements' => ['method', 'property'],
        ],
    ])
    ->setUnsupportedPhpVersionAllowed(true)
    ->setRiskyAllowed(true)
    ->setUsingCache(true);
