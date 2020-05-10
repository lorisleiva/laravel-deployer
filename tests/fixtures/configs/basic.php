<?php

return [

    'options' => [
        'application' => env('APP_NAME', 'Laravel'),
        'repository' => '{{repo}}',
        'upload_path' => '{{repo}}',
        'writable_mode' => 'chmod',
        'local_deploy_path' => '{{tmp}}/local_build',
        'local_cache_repository' => '{{repo}}',
        'local_deploy_shared' => false,
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
            'artisan:storage:link',
            'artisan:view:clear',
            'artisan:config:cache',
            'artisan:optimize',
        ],
        'rollback' => [
            'fpm:reload',
        ],
    ],

    'include' => [
        'fixtures/recipes/mocks.php',
    ],

];
