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
            'target' => '.cursorrules',
            'label' => 'Cursor',
        ],
        'claude' => [
            'target' => 'CLAUDE.md',
            'label' => 'Claude Code',
        ],
        'antigravity' => [
            'target' => '.antigravityrules',
            'label' => 'Google Anti-Gravity',
        ],
        'windsurf' => [
            'target' => '.windsurfrules',
            'label' => 'Windsurf',
        ],
        'copilot' => [
            'target' => '.github/copilot-instructions.md',
            'label' => 'GitHub Copilot',
        ],
        'gemini' => [
            'target' => '.gemini-instructions.md',
            'label' => 'Gemini (Instructions)',
        ],
        'gemini_md' => [
            'target' => 'GEMINI.md',
            'label' => 'Gemini CLI (GEMINI.md)',
        ],
        'boost' => [
            'target' => '.boost-rules.md',
            'label' => 'Laravel Boost (MCP)',
        ],
        'agents' => [
            'target' => 'agents.md',
            'label' => 'Codex / AI Agents (agents.md)',
        ],
        'amazonq' => [
            'target' => '.amazonq/instructions.md',
            'label' => 'Amazon Q',
        ],
        'aider' => [
            'target' => '.aider.conf.yml',
            'label' => 'Aider',
            'template' => 'aider.stub',
        ],
        'continue' => [
            'target' => '.continue/instructions.md',
            'label' => 'Continue.dev',
        ],
        'cline' => [
            'target' => '.clinerules',
            'label' => 'Cline',
        ],
        'general' => [
            'target' => 'ai-instructions.md',
            'label' => 'General AI Instructions',
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
