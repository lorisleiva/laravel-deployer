<?php

namespace Lorisleiva\LaravelDeployer;

use Illuminate\Filesystem\Filesystem;

class DeployFileGenerator
{
    protected $filesystem;

    protected $useForge = false;

    protected $replacements = [
        'shared_files_add_or_set' => 'add',
        'shared_dirs_add_or_set' => 'add',
        'writable_dirs_add_or_set' => 'add',
        'shared_files_comment' => '',
        'shared_dirs_comment' => '',
        'writable_dirs_comment' => '',
        'hostname' => 'example.com',
        'host_stage' => '',
        'host_deploy_path' => '/var/www/html',
        'host_user' => 'root',
        'npm_build' => 'production',
        'hook_horizon_command' => 'before',
        'hook_horizon_other_hook' => 'deploy:symlink',
    ];

    protected $blocks = [
        'default_stage' => false,
        'host_block_stage' => false,
        'host_block_deploy_path' => true,
        'host_block_user' => false,
        'localhost' => false,
        'hook_npm' => false,
        'hook_migrations' => false,
        'hook_horizon' => false,
        'hook_empty' => true,
    ];

    public function __construct()
    {
        $this->filesystem = resolve(Filesystem::class);

        $basePath = base_path();
        $defaultApplicationName = env('APP_NAME', 'Application');

        $this->replacements = array_merge($this->replacements, [
            'application' => $defaultApplicationName,
            'application_snake_case' => snake_case($defaultApplicationName),
            'repository' => exec("cd $basePath && git config --get remote.origin.url") ?? '',
        ]);
    }

    /**
     * Set up the application name.
     *
     * @param string $value
     * @return DeployFileGenerator
     */
    public function application($value)
    {
        $this->replacements['application'] = $value;
        $this->replacements['application_snake_case'] = snake_case($value);

        return $this;
    }

    /**
     * Set up the repository URL.
     *
     * @param string $value
     * @return DeployFileGenerator
     */
    public function repository($value)
    {
        $this->replacements['repository'] = $value;

        return $this;
    }

    /**
     * Choose to set instead of add files or directories.
     *
     * @param string $key
     * @return DeployFileGenerator
     */
    public function override($key)
    {
        $humanReadableKey = str_replace('dirs', 'directories', $key);
        $humanReadableKey = str_replace('_', ' ', $humanReadableKey);
        $this->replacements["{$key}_add_or_set"] = 'set';
        $this->replacements["{$key}_comment"] = " /* List your $humanReadableKey here. */ ";

        return $this;
    }

    /**
     * Set up the hostname.
     *
     * @param string $value
     * @return DeployFileGenerator
     */
    public function hostname($value)
    {
        $oldHostname = $this->replacements['hostname'];
        $this->replacements['hostname'] = $value;

        if ($this->useForge) {
            $this->updateUnchangedDeploymentPathForForge($oldHostname);
        }

        return $this;
    }

    /**
     * Set up the host stage.
     *
     * @param string $value
     * @return DeployFileGenerator
     */
    public function stage($value)
    {
        $this->replacements['host_stage'] = $value;
        $this->blocks['host_block_stage'] = true;

        return $this;
    }

    /**
     * Set up the host deployment path.
     *
     * @param string $value
     * @return DeployFileGenerator
     */
    public function deploymentPath($value)
    {
        $this->replacements['host_deploy_path'] = $value;
        $this->blocks['host_block_deploy_path'] = true;

        return $this;
    }

    /**
     * Set up the host user.
     *
     * @param string $value
     * @return DeployFileGenerator
     */
    public function user($value)
    {
        $this->replacements['host_user'] = $value;
        $this->blocks['host_block_user'] = true;

        return $this;
    }

    /**
     * Set up to build assets via npm.
     *
     * @param string $mode
     * @return DeployFileGenerator
     */
    public function useNpm($mode = 'production')
    {
        $this->replacements['npm_build'] = $mode;
        $this->blocks['hook_npm'] = true;
        $this->blocks['hook_empty'] = false;

        return $this;
    }

    /**
     * Set up to migrate when deploying.
     *
     * @return DeployFileGenerator
     */
    public function migrate()
    {
        $this->blocks['hook_migrations'] = true;
        $this->blocks['hook_empty'] = false;

        if ($this->blocks['hook_horizon']) {
            $this->replacements['hook_horizon_command'] = 'after';
            $this->replacements['hook_horizon_other_hook'] = 'artisan:migrate';
        }

        return $this;
    }

    /**
     * Set up to terminate horizon when deploying.
     *
     * @return DeployFileGenerator
     */
    public function terminateHorizon()
    {
        $this->blocks['hook_horizon'] = true;
        $this->blocks['hook_empty'] = false;

        if ($this->blocks['hook_migrations']) {
            $this->replacements['hook_horizon_command'] = 'after';
            $this->replacements['hook_horizon_other_hook'] = 'artisan:migrate';
        }

        return $this;
    }

    /**
     * Set up a local version of the host to enable 
     * deploying directly from the server.
     *
     * @param string $stage
     * @return DeployFileGenerator
     */
    public function localhost($stage = 'local')
    {
        $this->blocks['localhost'] = true;
        $this->blocks['default_stage'] = true;
        $this->stage('prod');

        return $this;
    }

    /**
     * Set up defaults values more suitable for forge servers.
     *
     * @return DeployFileGenerator
     */
    public function useForge()
    {
        $this->useForge = true;
        $this->localhost();
        $this->user('forge');
        $this->updateUnchangedDeploymentPathForForge();

        return $this;
    }

    /**
     * Return the current value of a replacement variable.
     *
     * @param string $key
     * @return string
     */
    public function get($key)
    {
        return array_get($this->replacements, $key);
    }

    /**
     * Return the content of the raw deploy.stub file.
     *
     * @return string content of `stubs/deploy.stub`.
     */
    public function getStub()
    {
        return $this->filesystem->get(__DIR__ . '/stubs/deploy.stub');
    }

    /**
     * return the deploy.stub file as a string after it has been 
     * parsed with the information provided by the setters.
     *
     * @return string the parsed content of `stubs/deploy.stub`.
     */
    public function getParsedStub()
    {
        $stub = $this->getStub();

        // Parse replacement variables with their value.
        foreach ($this->replacements as $variable => $value) {
            $stub = preg_replace('/{{' . $variable . '}}/', $value, $stub);
        }

        // Show or hide variable blocks of code.
        foreach ($this->blocks as $variable => $show) {
            $stub = preg_replace('/{{' . $variable . ':([^}]*)}}/', $show ? '$1' : '', $stub);
        }

        // Trim empty lines at the end of file.
        $stub = preg_replace('/\n+$/', '', $stub);

        // Ensure stub has no more than two consecutive empty lines.
        $stub = preg_replace('/\n{3,}/', "\n\n", $stub);

        return $stub;
    }

    /**
     * Parse the deploy.stub file and copy its content onto a new 
     * `deploy.php` file at the root of the Laravel project.
     */
    public function generate()
    {
        $this->filesystem->put(base_path('deploy.php'), $this->getParsedStub());
    }

    protected function updateUnchangedDeploymentPathForForge($oldHostname = '')
    {
        $unchangedDeployPaths = [
            '/var/www/html',
            '/home/forge/' . $oldHostname,
        ];

        if (in_array($this->replacements['host_deploy_path'], $unchangedDeployPaths)) {
            $this->deploymentPath('/home/forge/' . $this->replacements['hostname']);
        }
    }
}