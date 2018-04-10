<?php

namespace Lorisleiva\LaravelDeployer;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Filesystem\Filesystem;
use Lorisleiva\LaravelDeployer\Concerns\RendersCode;

class ConfigFile implements Arrayable
{
    use RendersCode;

    const REPLACEMENT_KEYS = [
        'default',
        'strategies',
        'hooks.start',
        'hooks.build',
        'hooks.ready',
        'hooks.done',
        'hooks.success',
        'hooks.fail',
        'options',
        'hosts',
        'localhost',
        'include',
        'custom_deployer_file',
    ];

    protected $configs;
    protected $filesystem;

    public function __construct($configs)
    {
        $this->configs = collect($configs);
        $this->filesystem = app(Filesystem::class);
    }

    public function toArray()
    {
        return $this->configs->toArray();
    }

    /**
     * Parse the `config.stub` file and copy its content onto a new 
     * `deploy.php` file in the config folder of the Laravel project.
     */
    public function store($path = 'config/deploy.php')
    {
        $dir = dirname(base_path($path));

        if (! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $this->filesystem->put(base_path($path), (string) $this);
    }

    /**
     * Return the config file as a string after it has been parsed
     * from the `config.stub` file with the `configs` property.
     *
     * @return string
     */
    public function __toString()
    {
        $stub = $this->filesystem->get(__DIR__ . '/stubs/config.stub');

        foreach (static::REPLACEMENT_KEYS as $key) {
            $indent = substr_count($key, '.') + 1;
            $value = $this->render(array_get($this->configs, $key), $indent);
            $stub = preg_replace('/{{' . $key . '}}/', $value, $stub);
        };

        return $stub;
    }
}