<?php

namespace Lorisleiva\LaravelDeployer\Test\Unit;

use Lorisleiva\LaravelDeployer\ConfigFile;
use Lorisleiva\LaravelDeployer\ConfigFileBuilder;
use Lorisleiva\LaravelDeployer\Test\TestCase;

class ConfigFileTest extends TestCase
{
    /** @test */
    function it_can_render_a_config_file_that_is_equivalent_to_its_content()
    {
        $config = new ConfigFile([
            'default' => 'basic',
            'strategies' => [],
            'hooks' => [
                'start'   => [],
                'build'   => [],
                'ready'   => [
                    'artisan:storage:link',
                    'artisan:view:clear',
                    'artisan:cache:clear',
                    'artisan:config:cache',
                    'artisan:optimize',
                ],
                'done'    => [],
                'fail'    => [],
                'success' => [],
            ],
            'options' => [
                'application' => "env('APP_NAME', 'Laravel')",
            ],
            'hosts' => [
                'example.com' => [
                    'deploy_path' => '/var/www/example.com',
                    'user' => 'root',
                    'stage' => 'prod',
                ]
            ],
            'localhost' => [],
            'include' => [],
            'custom_deployer_file' => false,
        ]);

        $string = $config->__toString();
        $string = str_replace('<?php', '', $string);
        $evaluatedString = eval($string);

        $expectedArray = $config->toArray();
        array_set($expectedArray, 'options.application', 'Laravel');

        $this->assertEquals($evaluatedString, $expectedArray);
    }
}