<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('vendor')
    ->in(__DIR__);

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR2' => true,
        'array_syntax' => ['syntax' => 'short'],

        'blank_line_after_namespace' => true,
        'blank_line_before_return' => true,
        'concat_space' => ['spacing' => 'one'],
        'function_typehint_space' => true,
        'hash_to_slash_comment' => true,
        'include' => true,
        'lowercase_cast' => true,
        'new_with_braces' => true,
        'no_blank_lines_before_namespace' => true,
        'no_empty_statement' => true,
        'no_extra_consecutive_blank_lines' => ['use'],
        'no_leading_import_slash' => true,
        'no_unused_imports' => true,
        'no_whitespace_in_blank_line' => true,
        'object_operator_without_whitespace' => true,
        'phpdoc_align' => true,
        'phpdoc_scalar' => true,
        'phpdoc_types' => true,
        'short_scalar_cast' => true,
        'single_blank_line_at_eof' => true,
        'single_quote' => true,
        'trailing_comma_in_multiline_array' => true,
        'ordered_imports' => true,
        'phpdoc_order' => true,
    ])
    ->setFinder($finder);
