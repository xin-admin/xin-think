<?php

$header = <<<'EOF'
 +----------------------------------------------------------------------
 | XinAdmin [ A Full stack framework ]
 +----------------------------------------------------------------------
 | Copyright (c) 2023~2024 http://xinadmin.cn All rights reserved.
 +----------------------------------------------------------------------
 | Apache License ( http://www.apache.org/licenses/LICENSE-2.0 )
 +----------------------------------------------------------------------
 | Author: 小刘同学 <2302563948@qq.com>
 +----------------------------------------------------------------------
EOF;

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR2' => true,
        '@Symfony' => true,
        '@DoctrineAnnotation' => true,
        '@PhpCsFixer' => true,
        'header_comment' => [
            'header' => $header,
            'separate' => 'none',
            'location' => 'after_open',
        ],
        'array_syntax' => [
            'syntax' => 'short'
        ],
        'list_syntax' => [
            'syntax' => 'short'
        ],
        'concat_space' => [
            'spacing' => 'one'
        ],
        'blank_line_before_statement' => [
            'statements' => [
                'declare',
            ],
        ],
        'general_phpdoc_annotation_remove' => [
            'annotations' => [
                'author'
            ],
        ],
        'ordered_imports' => [
            'imports_order' => [
                'class', 'function', 'const',
            ],
            'sort_algorithm' => 'alpha',
        ],
        'single_line_comment_style' => [
            'comment_types' => [
            ],
        ],
        'yoda_style' => [
            'always_move_variable' => false,
            'equal' => false,
            'identical' => false,
        ],
        'phpdoc_align' => [
            'align' => 'left',
        ],
        'multiline_whitespace_before_semicolons' => [
            'strategy' => 'no_multi_line',
        ],
        'constant_case' => [
            'case' => 'lower',
        ],
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => true,
        ],
        'class_attributes_separation' => true,
        'combine_consecutive_unsets' => true,
        'declare_strict_types' => false,
        'linebreak_after_opening_tag' => true,
        'lowercase_static_reference' => true,
        'no_useless_else' => true,
        'no_unused_imports' => true,
        'not_operator_with_successor_space' => true,
        'not_operator_with_space' => false,
        'ordered_class_elements' => true,
        'php_unit_strict' => false,
        'phpdoc_separation' => false,
        'single_quote' => true,
        'standardize_not_equals' => true,
        'multiline_comment_opening_closing' => true,
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->exclude('bin')
            ->exclude('public')
            ->exclude('runtime')
            ->exclude('vendor')
            ->in(__DIR__)
    )
    ->setUsingCache(false);
