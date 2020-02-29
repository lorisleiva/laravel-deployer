<?php

return [

    'options' => [
        'application' => env('APP_NAME', 'Laravel'),
        'repository' => '{{repo}}',
        'writable_mode' => 'chown',
        'branch' => null,
        'debug' => true,
    ],

    'localhost' => [
        'deploy_path' => '{{server}}',
    ],

    'hooks' => [
        'build' => [
            'npm:install',
            'npm:development',
        ],
        'ready' => [
            'artisan:cache:clear',
            'artisan:optimize',
        ],
    ],

    'include' => [
        'fixtures/recipes/mocks.php',
    ],

];