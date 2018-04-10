<?php

namespace Lorisleiva\LaravelDeployer;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Filesystem\Filesystem;

class ConfigFile implements Arrayable
{
    const REPLACEMENT_KEYS = [
        'default',
        'strategies',
        'hooks.start',
        'hooks.build',
        'hooks.ready',
        'hooks.done',
        'hooks.fail',
        'hooks.success',
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

    protected function render($value, $indent = 1)
    {
        switch (gettype($value)) {
            case 'array':
                return $this->renderArray($value, $indent);
            case 'string':
                return starts_with($value, 'env(') ? $value : "'$value'";
            case 'boolean':
                return $value ? 'true' : 'false';
            default:
                return is_null($value) ? 'null' : $value;
        }
    }

    protected function renderArray($value, $indent)
    {
        $indentParent = str_repeat('    ', $indent);
        $indentChildren = str_repeat('    ', $indent + 1);

        if (empty($value)) {
            return "[\n$indentChildren//\n$indentParent]";
        }

        $arrayContent = collect($value)
            ->map(function ($v, $k) use ($indent, $indentChildren) {
                $v = $this->render($v, $indent + 1);
                return is_string($k) 
                    ? "$indentChildren'$k' => $v"
                    : "$indentChildren$v";
            })
            ->implode(",\n");

        return "[\n$arrayContent,\n$indentParent]";
    }
}