<?php

namespace Lorisleiva\LaravelDeployer;

use Illuminate\Support\Arr;
use Lorisleiva\LaravelDeployer\ConfigFile;

class ConfigFileBuilder
{
    const DEFAULT_PHP_VERSION = '7.3';

    protected $laravelHooks = [
        'artisan:storage:link',
        'artisan:view:clear',
        'artisan:config:cache',
    ];

    protected $lumenHooks = [
        //
    ];

    protected $configs = [
        'default' => 'basic',
        'strategies' => [],
        'hooks' => [
            'start' => [],
            'build' => [],
            'ready' => [],
            'done' => [],
            'success' => [],
            'fail' => [],
            'rollback' => [],
        ],
        'options' => [
            'application' => "env('APP_NAME', 'Laravel')",
        ],
        'hosts' => [
            'example.com' => [
                'deploy_path' => '/var/www/example.com',
                'user' => 'root',
            ]
        ],
        'localhost' => [],
        'include' => [],
        'custom_deployer_file' => false,
    ];


    public function __construct()
    {
        $basePath = base_path();
        $this->set('options.repository', exec("cd $basePath && git config --get remote.origin.url") ?? '');

        $lumen = preg_match('/Lumen/', app()->version());
        $this->set('hooks.ready', $lumen ? $this->lumenHooks : $this->laravelHooks);
    }

    /**
     * Return the configuration value at the given key.
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->configs, $key, $default);
    }

    /**
     * Update the configuration array with the given key/value pair.
     *
     * @return ConfigFileGenerator
     */
    public function set($key, $value)
    {
        Arr::set($this->configs, $key, $value);

        return $this;
    }

    /**
     * Append the given value to the configuration array at the given key.
     *
     * @return ConfigFileGenerator
     */
    public function add($key, $value)
    {
        $array = Arr::get($this->configs, $key);

        if (is_array($array)) {
            $array[] = $value;
            Arr::set($this->configs, $key, $array);
        }


        return $this;
    }

    /**
     * Return current host configurations at the given key.
     *
     * @return mixed
     */
    public function getHost($key)
    {
        return Arr::get(head($this->configs['hosts']), $key);
    }

    /**
     * Return the name of the first host in the configurations.
     *
     * @return string
     */
    public function getHostname()
    {
        return array_search(head($this->configs['hosts']), $this->configs['hosts']);
    }

    /**
     * Update the host configurations with the given key/value pair.
     *
     * @return ConfigFileGenerator
     */
    public function setHost($key, $value)
    {
        $hostname = $this->getHostname();

        if ($key !== 'name') {
            $this->configs['hosts'][$hostname][$key] = $value;
            return $this;
        }

        if ($hostname === $value) {
            return $this;
        }

        $this->configs['hosts'][$value] = $this->configs['hosts'][$hostname];
        unset($this->configs['hosts'][$hostname]);
        $this->setHost('deploy_path', "/var/www/$value");

        return $this;
    }

    /**
     * Set up defaults values more suitable for forge servers.
     *
     * @return ConfigFileGenerator
     */
    public function useForge($phpVersion = self::DEFAULT_PHP_VERSION)
    {
        $this->reloadFpm($phpVersion);
        $this->setHost('deploy_path', '/home/forge/' . $this->getHostname());
        $this->setHost('user', 'forge');

        return $this;
    }

    /**
     * Reload PHP-FPM after every deployment.
     *
     * @param string $phpVersion The php-fpm version to reload
     * @return ConfigFileGenerator
     */
    public function reloadFpm($phpVersion = self::DEFAULT_PHP_VERSION)
    {
        $this->add('hooks.done', 'fpm:reload');
        $this->add('hooks.rollback', 'fpm:reload');
        $this->set('options.php_fpm_service', "php$phpVersion-fpm");

        return $this;
    }

    /**
     * Build a config file object based on the information
     * collected so far.
     *
     * @return ConfigFile
     */
    public function build()
    {
        return new ConfigFile($this->configs);
    }
}
