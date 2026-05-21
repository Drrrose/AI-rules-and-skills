<?php

namespace Drose\LaravelAiRules\Console\Commands;

use Drose\LaravelAiRules\Support\PlaceholderResolver;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PublishAiRulesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:rules 
                            {--force : Overwrite existing rules files} 
                            {--only= : Comma-separated list of files to publish} 
                            {--except= : Comma-separated list of files to exclude} 
                            {--check : Check if published rules match current stubs}
                            {--dry-run : Show what would be published without making changes}
                            {--list : List all available rule targets}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish scalable AI coding guidelines (Laravel 13+ optimized)';

    /**
     * Execute the console command.
     */
    public function handle(PlaceholderResolver $resolver): int
    {
        $allFiles = config('ai-rules.files', []);

        if ($this->option('list')) {
            $this->info('Available rule targets:');
            foreach (array_keys($allFiles) as $target) {
                $this->line("- {$target}");
            }
            return 0;
        }

        $placeholders = $resolver->resolve(config('ai-rules.placeholders', []));
        
        $only = $this->option('only') ? array_map('trim', explode(',', $this->option('only'))) : [];
        $except = $this->option('except') ? array_map('trim', explode(',', $this->option('except'))) : [];
        
        $filesToProcess = collect($allFiles)->filter(function ($config, $key) use ($only, $except) {
            $target = $config['target'] ?? $key;
            // Match by exact target path or by the configuration key
            $matchesOnly = empty($only) || in_array($target, $only) || in_array($key, $only);
            $matchesExcept = !empty($except) && (in_array($target, $except) || in_array($key, $except));
            return $matchesOnly && !$matchesExcept;
        });

        if ($filesToProcess->isEmpty()) {
            $this->warn('No files matched the specified criteria.');
            return 0;
        }

        $needsUpdate = false;
        $defaultStubPath = config('ai-rules.stub_path', base_path('stubs/ai-rules.stub'));
        $packageStubDir = realpath(__DIR__ . '/../../../stubs') ?: (__DIR__ . '/../../../stubs');

        $publishedTargets = [];

        foreach ($filesToProcess as $key => $config) {
            $target = $config['target'] ?? $key;
            $template = $config['template'] ?? $defaultStubPath;

            $templatePath = $this->resolveTemplatePath($template, $packageStubDir);

            if (!$templatePath || !File::exists($templatePath)) {
                $this->error("Template not found for {$target}: {$template}");
                continue;
            }

            $content = $resolver->replace(File::get($templatePath), $placeholders);
            $fullPath = base_path($target);

            $existingContent = File::exists($fullPath) ? File::get($fullPath) : null;
            $normalizedContent = trim($content);

            if ($this->option('check')) {
                if ($existingContent === null || !str_contains($existingContent, $normalizedContent)) {
                    $this->warn("File {$target} is out of date or missing.");
                    $needsUpdate = true;
                }
                continue;
            }

            if ($this->option('dry-run')) {
                if ($existingContent === null) {
                    $this->info("[Dry Run] would create: {$target}");
                } elseif (!str_contains($existingContent, $normalizedContent)) {
                    $this->info("[Dry Run] would append to: {$target}");
                } else {
                    $this->info("[Dry Run] is already up to date: {$target}");
                }
                continue;
            }

            $dir = dirname($fullPath);
            if (!File::isDirectory($dir)) {
                File::makeDirectory($dir, 0755, true);
            }

            if ($existingContent !== null) {
                if ($this->option('force')) {
                    File::put($fullPath, $content);
                    $this->info("Overwrote AI rules in: {$target}");
                    $publishedTargets[] = $target;
                } elseif (str_contains($existingContent, $normalizedContent)) {
                    $this->line("File {$target} already contains the rules.");
                    $publishedTargets[] = $target;
                } else {
                    $appendContent = rtrim($existingContent) . "\n\n" . ltrim($content);
                    File::put($fullPath, $appendContent);
                    $this->info("Appended AI rules to: {$target}");
                    $publishedTargets[] = $target;
                }
            } else {
                File::put($fullPath, $content);
                $this->info("Published AI rules to: {$target}");
                $publishedTargets[] = $target;
            }
        }

        if (!$this->option('dry-run') && !$this->option('check') && !empty($publishedTargets)) {
            $this->updateGitignore($publishedTargets);
        }

        if ($this->option('check')) {
            return $needsUpdate ? 1 : 0;
        }

        return 0;
    }

    /**
     * Update the .gitignore file to include the published rule files and 3rd party AI files.
     */
    protected function updateGitignore(array $targets): void
    {
        $gitignorePath = base_path('.gitignore');
        
        if (!File::exists($gitignorePath)) {
            return;
        }

        $content = File::get($gitignorePath);
        $header = "\n# Laravel AI Rules\n";
        
        // Combine our targets with common 3rd party AI files that should be ignored
        $allPatterns = array_merge($targets, [
            '.boost-mcp.json',      // Laravel Boost MCP configuration
            '.guidelines-skills.json', // Spatie Guidelines Skills
            '.boost/',              // Laravel Boost internal directory
        ]);

        $linesToAdd = [];
        foreach ($allPatterns as $pattern) {
            if (!str_contains($content, $pattern)) {
                $linesToAdd[] = $pattern;
            }
        }

        if (empty($linesToAdd)) {
            return;
        }

        if (!str_contains($content, trim($header))) {
            $content = rtrim($content) . "\n" . $header;
        }

        foreach ($linesToAdd as $line) {
            $content = rtrim($content) . "\n" . $line;
        }

        File::put($gitignorePath, rtrim($content) . "\n");
        $this->info("Updated .gitignore with the AI rules and 3rd party AI patterns.");
    }

    /**
     * Resolve the absolute path to the template.
     */
    protected function resolveTemplatePath(string $template, string $packageStubDir): ?string
    {
        // 1. Check if the exact path exists
        if (File::exists($template)) {
            return $template;
        }
        
        // 2. If it is a simple filename like 'aider.stub', check base_path('stubs/')
        $inBaseStubs = base_path('stubs/' . $template);
        if (File::exists($inBaseStubs)) {
            return $inBaseStubs;
        }

        // 3. Check the package's internal stubs directory
        $inPackageStubs = $packageStubDir . '/' . basename($template);
        if (File::exists($inPackageStubs)) {
            return $inPackageStubs;
        }

        return null;
    }
}
