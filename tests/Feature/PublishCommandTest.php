<?php

use Illuminate\Support\Facades\File;

beforeEach(function () {
    $allFiles = config('ai-rules.files', []);
    $this->filesToClean = collect($allFiles)->map(fn($config, $key) => $config['target'] ?? $key)->values()->toArray();

    foreach ($this->filesToClean as $file) {
        $path = base_path($file);
        if (File::exists($path)) {
            File::delete($path);
        }
        $dir = dirname($path);
        if ($dir !== base_path() && File::isDirectory($dir) && count(File::allFiles($dir)) === 0) {
            // Only cleanup if directory is empty and not root
            File::deleteDirectory($dir); 
        }
    }
});

it('can publish all ai rules files', function () {
    $this->artisan('ai:rules')
         ->assertExitCode(0);

    foreach ($this->filesToClean as $file) {
        expect(base_path($file))->toBeFile();
    }
});

it('can publish specific files using --only', function () {
    $this->artisan('ai:rules --only=.cursorrules,CLAUDE.md')
         ->assertExitCode(0);

    expect(base_path('.cursorrules'))->toBeFile();
    expect(base_path('CLAUDE.md'))->toBeFile();
    expect(base_path('.clinerules'))->not->toBeFile();
});

it('can exclude files using --except', function () {
    $this->artisan('ai:rules --except=.cursorrules,CLAUDE.md')
         ->assertExitCode(0);

    expect(base_path('.cursorrules'))->not->toBeFile();
    expect(base_path('CLAUDE.md'))->not->toBeFile();
    expect(base_path('.clinerules'))->toBeFile();
});

it('replaces placeholders in published files', function () {
    $this->artisan('ai:rules --only=.cursorrules')
         ->assertExitCode(0);

    $content = File::get(base_path('.cursorrules'));
    
    // Testbench defaults
    expect($content)->toContain('Laravel'); 
    expect($content)->toContain('PHPUnit'); // Default for testbench unless Pest.php exists
});

it('can dry-run without creating files', function () {
    $this->artisan('ai:rules --dry-run')
         ->expectsOutputToContain('[Dry Run] would create: .cursorrules')
         ->assertExitCode(0);

    expect(base_path('.cursorrules'))->not->toBeFile();
});

it('fails check mode when files are missing', function () {
    $this->artisan('ai:rules --check')
         ->assertExitCode(1);
});

it('passes check mode when files are up to date', function () {
    $this->artisan('ai:rules')
         ->assertExitCode(0);

    $this->artisan('ai:rules --check')
         ->assertExitCode(0);
});

it('appends rules without force flag', function () {
    File::put(base_path('.cursorrules'), 'custom content');

    $this->artisan('ai:rules --only=.cursorrules')
         ->expectsOutputToContain('Appended AI rules to: .cursorrules')
         ->assertExitCode(0);

    $content = File::get(base_path('.cursorrules'));
    expect($content)->toContain('custom content');
    expect($content)->not->toBe('custom content');
    // It should not duplicate if run again
    $this->artisan('ai:rules --only=.cursorrules')
         ->expectsOutputToContain('already contains the rules')
         ->assertExitCode(0);

    $this->artisan('ai:rules --only=.cursorrules --force')
         ->assertExitCode(0);

    expect(File::get(base_path('.cursorrules')))->not->toContain('custom content');
});
