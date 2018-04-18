<?php

namespace Lorisleiva\LaravelDeployer;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Filesystem\Filesystem;
use Lorisleiva\LaravelDeployer\Concerns\RendersCode;

class DeployFile
{
    use RendersCode;
    
    const REPLACEMENT_KEYS = [
        'include',
        'default',
        'options',
        'hosts',
        'localhost',
        'strategies',
        'hooks',
    ];

    protected $data;
    protected $filesystem;

    public function __construct($data = [])
    {
        $this->data = collect($data);
        $this->filesystem = app(Filesystem::class);
    }

    public function get($key)
    {
        return collect($this->data->get($key, []));
    }

    public function updateStrategy($strategy)
    {
        if (is_string($strategy)) {
            $this->data->put('default', $strategy);
        }

        return $this;
    }

    public function store()
    {
        $path = __DIR__ . '/../../.build/deploy.php';
        $dir = dirname($path);

        if (! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $this->filesystem->put($path, (string) $this);

        return $path;
    }

    public function __toString()
    {
        $stub = $this->filesystem->get(__DIR__ . '/stubs/deploy.stub');

        foreach (static::REPLACEMENT_KEYS as $key) {
            $value = call_user_func([$this, 'render' . ucfirst($key)]);
            $stub = preg_replace('/{{' . $key . '}}/', $value, $stub);
        };

        // Trim empty lines at the end of file.
        $stub = preg_replace('/\n+$/', '', $stub);

        // Ensure stub has no more than two consecutive empty lines.
        $stub = preg_replace('/\n{3,}/', "\n\n", $stub);

        return $stub;
    }

    protected function renderDefault()
    {
        $default = $this->data->get('default', 'basic');
        return "set('strategy', '$default');";
    }

    protected function renderInclude()
    {
        return $this->get('include')
            ->map(function ($include) {
                return "require '$include';";
            })
            ->implode("\n");
    }

    protected function renderStrategies()
    {
        return $this->get('strategies')
            ->map(function ($tasks) {
                return collect($tasks)->map(function ($task) {
                    return "    '$task',";
                })->implode("\n");
            })
            ->map(function ($tasks, $strategy) {
                $title = title_case(str_replace('_', ' ', $strategy)) . ' Strategy';
                $slug = snake_case($strategy);

                return "desc('$title');\ntask('strategy:$slug', [\n$tasks\n]);";
            })
            ->implode("\n\n");
    }

    protected function renderOptions()
    {
        return $this->get('options')
            ->map(function ($value, $key) {
                $value = $this->render($value, 0, false);
                return "set('$key', $value);";
            })
            ->implode("\n");
    }

    protected function renderHosts()
    {
        return $this->get('hosts')
            ->map(function ($options) {
                return $this->renderHostOptions($options);
            })
            ->map(function ($options, $hostname) {
                return "host('$hostname')$options;";
            })
            ->implode("\n\n");
    }

    protected function renderLocalhost()
    {
        $options = $this->renderHostOptions($this->get('localhost'));

        return empty($options) ? '' : "localhost()$options;";
    }

    protected function renderHooks()
    {
        return $this->get('hooks')
            ->flatMap(function ($tasks, $hook) {
                return collect($tasks)->map(function ($task) use ($hook) {
                    switch ($hook) {
                        case 'success':
                            return "after('success', '$task');";
                        case 'fail':
                            return "after('deploy:fail', '$task');";
                        default:
                            return "after('hook:$hook', '$task');";
                    }
                });
            })
            ->implode("\n");
    }

    protected function renderHostOptions($options)
    {
        $options = collect($options)->map(function ($value, $key) {
            $value = $this->render($value, 1, false);
            
            return in_array($key, ['stage', 'user'])
                ? "    ->$key($value)"
                : "    ->set('$key', $value)";
        })->implode("\n");
        
        return empty($options) ? '' : "\n$options";
    }
}