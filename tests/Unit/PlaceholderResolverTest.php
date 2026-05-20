<?php

use Drose\LaravelAiRules\Support\PlaceholderResolver;

it('resolves basic placeholders', function () {
    $resolver = new PlaceholderResolver();
    
    // We expect the default config for testbench/laravel to be used
    $placeholders = $resolver->resolve(['custom_key' => 'custom_value']);

    expect($placeholders)->toHaveKey('project_name')
        ->toHaveKey('laravel_version')
        ->toHaveKey('php_version')
        ->toHaveKey('test_runner')
        ->toHaveKey('installed_packages')
        ->toHaveKey('custom_key', 'custom_value');
});

it('replaces placeholders correctly in content', function () {
    $resolver = new PlaceholderResolver();
    
    $content = 'Hello {{ project_name }}, running PHP {{ php_version }}. Custom: {{ custom_key }}';
    
    $placeholders = [
        'project_name' => 'TestApp',
        'php_version' => '8.2.0',
        'custom_key' => 'Works',
    ];

    $replaced = $resolver->replace($content, $placeholders);
    
    expect($replaced)->toBe('Hello TestApp, running PHP 8.2.0. Custom: Works');
});
