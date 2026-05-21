<?php

namespace Drose\LaravelAiRules\Console\Commands;

use Illuminate\Console\Command;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\info;

class InstallAiRulesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:rules:install {--force : Overwrite existing rules files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Interactive setup for Laravel AI Rules';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        intro('Laravel AI Rules Setup');

        $allFiles = config('ai-rules.files', []);
        
        // Calculate padding for alignment
        $maxName = collect($allFiles)->map(fn($f) => strlen($f['name']))->max();
        $maxTarget = collect($allFiles)->map(fn($f) => strlen($f['target']))->max();

        $options = [];
        foreach ($allFiles as $key => $config) {
            $name = str_pad($config['name'], $maxName);
            $target = str_pad($config['target'], $maxTarget);
            $desc = $config['description'] ?? '';
            
            $options[$key] = "{$name}  ➜  {$target}  ({$desc})";
        }

        // Default selections: Boost, Gemini (instructions), and Gemini CLI (GEMINI.md)
        $defaults = ['boost', 'gemini', 'gemini_md'];

        $selected = multiselect(
            label: 'Select the AI Agents and guidelines to initialize:',
            options: $options,
            default: $defaults,
            scroll: 10,
            required: true,
            hint: 'Use space to select, enter to confirm.'
        );

        info('Publishing selected rules...');

        $targets = [];
        foreach ($selected as $key) {
            $targets[] = $allFiles[$key]['target'];
        }

        $this->call('ai:rules', [
            '--only' => implode(',', $targets),
            '--force' => $this->option('force'),
        ]);

        info('Configuring Laravel Boost...');
        $this->call('boost:install');

        outro('Setup completed successfully! Your AI guidelines are ready.');

        return 0;
    }
}
