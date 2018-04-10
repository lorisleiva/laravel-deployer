<?php

namespace Lorisleiva\LaravelDeployer;

use Illuminate\Filesystem\Filesystem;

class ConfigFileGenerator
{
    protected $filesystem;

    protected $defaultLaravelReadyHooks = [
        'artisan:storage:link',
        'artisan:view:clear',
        'artisan:cache:clear',
        'artisan:config:cache',
        'artisan:optimize',
    ];

    protected $defaultLumenReadyHooks = [
        'artisan:cache:clear',
        'artisan:optimize',
    ];

    protected $configs = [
        'default' => 'basic',
        'strategies' => [],
        'hooks' => [
            'start'   => [],
            'build'   => [],
            'ready'   => [],
            'done'    => [],
            'fail'    => [],
            'success' => [],
        ],
        'options' => [
            'application' => "env('APP_NAME', 'Laravel')",
        ],
        'hosts' => [
            'example.com' => [
                'deploy_path' => '/var/www/html',
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

        $this->filesystem = app(Filesystem::class);
        $this->set('options.repository', exec("cd $basePath && git config --get remote.origin.url") ?? '');
        
        $defaultReadyHooks = preg_match('/Lumen/', app()->version())
            ? $this->defaultLumenReadyHooks 
            : $this->defaultLaravelReadyHooks;

        $this->set('hooks.ready', $defaultReadyHooks);
    }

    /**
     * Return the configuration value at the given key.
     * 
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return array_get($this->configs, $key, $default);
    }

    /**
     * Update the configuration array with the given key/value pair.
     * 
     * @return ConfigFileGenerator
     */
    public function set($key, $value)
    {
        array_set($this->configs, $key, $value);

        return $this;
    }

    /**
     * Append the given value to the configuration array at the given key.
     * 
     * @return ConfigFileGenerator
     */
    public function add($key, $value)
    {
        $array = array_get($this->configs, $key);

        if (! is_array($array)) {
            return $this;
        }

        $array[] = $value;

        array_set($this->configs, $key, $array);

        return $this;
    }

    /**
     * Return current host configurations at the given key.
     * 
     * @return mixed
     */
    public function getHost($key)
    {
        return array_get(head($this->configs['hosts']), $key);
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
        return $this;
    }

    /**
     * Set up defaults values more suitable for forge servers.
     *
     * @return ConfigFileGenerator
     */
    public function useForge()
    {
        $this->add('hooks.done', 'fpm:reload');
        $this->set('options.php_fpm_service', 'php7.1-fpm');
        $this->setHost('deploy_path', '/home/forge/' . $this->getHostname());
        $this->setHost('user', 'forge');

        return $this;
    }

    /**
     * Return the content of the raw config.stub file.
     *
     * @return string content of `stubs/config.stub`.
     */
    public function getStub()
    {
        return $this->filesystem->get(__DIR__ . '/stubs/config.stub');
    }
    
    /**
     * Parse the `config.stub` file and copy its content onto a new 
     * `deploy.php` file in the config folder of the Laravel project.
     */
    public function generate()
    {
        $this->filesystem->put(config_path('deploy.php'), $this->getParsedStub());
    }

    /**
     * Return the config.stub file as a string after it has been 
     * parsed with the information provided by the setters.
     *
     * @return string the parsed content of `stubs/config.stub`.
     */
    public function getParsedStub()
    {
        $stub = $this->getStub();

        foreach ($this->getReplacementKeys() as $key) {
            $stub = $this->replace($key, $stub);
        };

        return $stub;
    }

    protected function getReplacementKeys()
    {
        $hooks = collect($this->configs['hooks'])->keys()->map(function ($key) {
            return 'hooks.' . $key;
        });

        return collect($this->configs)
            ->except('hooks')
            ->keys()
            ->merge($hooks)
            ->toArray();
    }

    protected function replace($key, $stub)
    {
        $indent = substr_count($key, '.') + 1;
        $value = $this->parseReplacement(array_get($this->configs, $key), $indent);

        return preg_replace('/{{' . $key . '}}/', $value, $stub);
    }

    protected function parseReplacement($value, $indentationLevel = 1)
    {
        if (is_array($value)) {
            $indentationParent = str_repeat('    ', $indentationLevel);
            $indentationChildren = str_repeat('    ', $indentationLevel + 1);

            $arrayContent = empty($value) ? "$indentationChildren//" : collect($value)
                ->map(function ($v, $k) use ($indentationLevel, $indentationChildren) {
                    $v = $this->parseReplacement($v, $indentationLevel + 1);
                    return is_string($k) 
                        ? "$indentationChildren'$k' => $v"
                        : "$indentationChildren$v";
                })
                ->implode(",\n");

            return "[\n$arrayContent,\n$indentationParent]";
        } 
        
        if (is_string($value)) {
            return starts_with($value, 'env(') ? $value : "'$value'";
        } 
        
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
            
        return is_null($value) ? 'null' : $value;
    }
}