<?php

namespace Drose\LaravelAiRules\Support;

use Illuminate\Support\Facades\File;

class PlaceholderResolver
{
    /**
     * Resolve project-specific placeholders.
     */
    public function resolve(array $configPlaceholders = []): array
    {
        $app = app();
        
        return array_merge([
            'project_name' => config('app.name', 'Laravel'),
            'laravel_version' => $app->version(),
            'php_version' => PHP_VERSION,
            'test_runner' => $this->detectTestRunner(),
            'installed_packages' => $this->getInstalledPackages(),
        ], $configPlaceholders);
    }

    /**
     * Replace placeholders in content.
     */
    public function replace(string $content, array $placeholders): string
    {
        foreach ($placeholders as $key => $value) {
            $content = str_replace("{{ {$key} }}", $value, $content);
        }

        return $content;
    }

    /**
     * Detect if Pest or PHPUnit is used.
     */
    public function detectTestRunner(): string
    {
        if (File::exists(base_path('tests/Pest.php'))) {
            return 'Pest';
        }

        return 'PHPUnit';
    }

    /**
     * Get a list of key installed packages.
     */
    public function getInstalledPackages(): string
    {
        $composerFile = base_path('composer.json');
        if (!File::exists($composerFile)) {
            return 'Unknown';
        }

        $composerContent = File::get($composerFile);
        $composer = json_decode($composerContent, true);
        
        if (!is_array($composer)) {
            return 'Unknown';
        }

        $packages = array_merge(
            $composer['require'] ?? [],
            $composer['require-dev'] ?? []
        );

        $interesting = [
            'laravel/framework',
            'laravel/boost',
            'spatie/guidelines-skills',
            'inertiajs/inertia-laravel',
            'livewire/livewire',
            'laravel/jetstream',
            'laravel/breeze',
        ];

        return collect($packages)
            ->filter(fn ($v, $k) => in_array($k, $interesting))
            ->keys()
            ->implode(', ');
    }
}
