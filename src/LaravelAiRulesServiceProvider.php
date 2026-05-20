<?php

namespace Drose\LaravelAiRules;

use Illuminate\Support\ServiceProvider;
use Drose\LaravelAiRules\Console\Commands\PublishAiRulesCommand;
use Drose\LaravelAiRules\Console\Commands\InstallAiRulesCommand;

class LaravelAiRulesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                PublishAiRulesCommand::class,
                InstallAiRulesCommand::class,
            ]);
            
            $this->publishes([
                __DIR__.'/../config/ai-rules.php' => config_path('ai-rules.php'),
            ], 'ai-rules-config');

            $this->publishes([
                __DIR__.'/../stubs/ai-rules.stub' => base_path('stubs/ai-rules.stub'),
            ], 'ai-rules-stubs');
        }
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/ai-rules.php', 'ai-rules'
        );
    }
}
