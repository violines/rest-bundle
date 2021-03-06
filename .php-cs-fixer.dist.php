<?php

$finder = PhpCsFixer\Finder::create()
    ->in([__DIR__ . '/src', __DIR__ . '/tests']);

$config = new PhpCsFixer\Config();
$config
    ->setRules([
        '@PSR2' => true,
        '@Symfony' => true,
        'cast_spaces' => ['space' => 'none'],
        'concat_space' => ['spacing' => 'one'],
        'native_function_invocation' => ['include' => ['@all']],
        'php_unit_set_up_tear_down_visibility' => true,
        'ordered_imports' => [
            'sort_algorithm' => 'alpha',
            'imports_order' => ['const', 'class', 'function'],
        ]
    ])
    ->setFinder($finder);

return $config;
