<?php

return [
    /*
     * The path to the stub file containing the AI rules.
     * When you publish the stubs, it will be placed in your base_path('stubs/ai-rules.stub').
     */
    'stub_path' => base_path('stubs/ai-rules.stub'),

    /*
     * The list of rule files that should be generated.
     * You can specify the 'target' path and an optional 'template' stub.
     * If no template is provided, the default 'stub_path' will be used.
     */
    'files' => [
        'cursor' => [
            'name' => 'Cursor',
            'target' => '.cursorrules',
            'description' => 'Best for Cursor IDE',
        ],
        'claude' => [
            'name' => 'Claude Code',
            'target' => 'CLAUDE.md',
            'description' => 'Optimized for Claude.ai',
        ],
        'antigravity' => [
            'name' => 'Anti-Gravity',
            'target' => '.antigravityrules',
            'description' => 'Google Anti-Gravity rules',
        ],
        'windsurf' => [
            'name' => 'Windsurf',
            'target' => '.windsurfrules',
            'description' => 'Codeium Flow support',
        ],
        'copilot' => [
            'name' => 'Copilot',
            'target' => '.github/copilot-instructions.md',
            'description' => 'GitHub Copilot instructions',
        ],
        'gemini' => [
            'name' => 'Gemini',
            'target' => '.gemini-instructions.md',
            'description' => 'Agentic instructions',
        ],
        'gemini_md' => [
            'name' => 'Gemini CLI',
            'target' => 'GEMINI.md',
            'description' => 'Workspace guidelines',
        ],
        'boost' => [
            'name' => 'Laravel Boost',
            'target' => '.boost-rules.md',
            'description' => 'MCP Context rules',
        ],
        'agents' => [
            'name' => 'AI Agents',
            'target' => 'agents.md',
            'description' => 'Universal Agent rules',
        ],
        'amazonq' => [
            'name' => 'Amazon Q',
            'target' => '.amazonq/instructions.md',
            'description' => 'Amazon Q support',
        ],
        'aider' => [
            'name' => 'Aider',
            'target' => '.aider.conf.yml',
            'description' => 'CLI Pair Programmer',
            'template' => 'aider.stub',
        ],
        'continue' => [
            'name' => 'Continue',
            'target' => '.continue/instructions.md',
            'description' => 'Continue.dev support',
        ],
        'cline' => [
            'name' => 'Cline',
            'target' => '.clinerules',
            'description' => 'Autonomous Agent rules',
        ],
        'general' => [
            'name' => 'General AI',
            'target' => 'ai-instructions.md',
            'description' => 'Fallback AI rules',
        ],
    ],

    /*
     * Placeholders that will be replaced in the stubs.
     * You can use {{ project_name }}, {{ laravel_version }}, etc.
     */
    'placeholders' => [
        'project_name' => env('APP_NAME', 'Laravel'),
    ],
];
