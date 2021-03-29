<?php

namespace Lorisleiva\LaravelDeployer\Test;

class DeploymentTestCase extends TestCase
{
    const TMP = __DIR__ . '/fixtures/tmp';
    const REPOSITORY = __DIR__ . '/fixtures/tmp/repository';
    const SERVER = __DIR__ . '/fixtures/tmp/server';

    protected $configs = 'basic';

    protected function getEnvironmentSetUp($app)
    {
        $app->setBasePath(static::REPOSITORY);

        if (file_exists(base_path('config/deploy.php'))) {
            $app['config']->set('deploy', include base_path('config/deploy.php'));
        }
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->resetTempDirectory();
    }

    public function tearDown(): void
    {
        parent::tearDown();
        $this->cleanupTempDirectory();
    }

    public function resetTempDirectory()
    {
        $this->cleanupTempDirectory();
        mkdir(static::TMP);
        mkdir(static::SERVER);

        $this->createRepository();
        $this->initializeGitOnRepository();
    }

    public function cleanupTempDirectory()
    {
        $this->exec('rm -rf ' . static::TMP);
        $this->exec('rm -rf ' . __DIR__ . '/../.build');
    }

    public function createRepository()
    {
        // Copy from static repository base.
        $this->exec('rsync -r ' . static::BASE_REPOSITORY . '/ ' . static::REPOSITORY);

        // Add symlink to Deployer binary.
        $this->runInRepository('mkdir -p vendor/bin');
        $this->runInRepository('ln -s ' . __DIR__ . '/../vendor/bin/dep vendor/bin/dep');

        // Add and parse config/deploy.php file.
        if ($configFile = realpath(static::CONFIGS . '/' . $this->configs . '.php')) {
            $this->runInRepository("cp $configFile config/deploy.php");
            $this->updateConfigFile(function ($content) {
                $content = str_replace('{{tmp}}', static::TMP, $content);
                $content = str_replace('{{repo}}', static::REPOSITORY, $content);
                return str_replace('{{server}}', static::SERVER, $content);
            });
        }
    }

    public function initializeGitOnRepository()
    {
        $this->runInRepository('rm -rf .git');
        $this->runInRepository("git init");
        $this->runInRepository("git add .");
        $this->runInRepository("git config user.name 'John Smith'");
        $this->runInRepository("git config user.email 'john.smith@example.com'");
        $this->runInRepository("git commit -m 'init commit'");
    }

    public function updateConfigFile($callback)
    {
        $content = file_get_contents(static::REPOSITORY . '/config/deploy.php');
        $content = $callback($content);
        file_put_contents(static::REPOSITORY . '/config/deploy.php', $content);
    }

    public function runInRepository($command)
    {
        return $this->exec("cd " . static::REPOSITORY . " && $command");
    }

    public function commitChanges()
    {
        $this->runInRepository('git add --all && git commit -m "changes"');
    }

    public function runInRoot($command)
    {
        return $this->exec("cd " . static::SERVER . " && $command");
    }

    public function runInCurrent($command)
    {
        return $this->exec("cd " . static::SERVER . "/current && $command");
    }

    public function assertSuccessfulDeployment()
    {
        $this->assertServerHas([
            'app/User.php',
            'app/Http/Controllers/Controller.php',
            'bootstrap',
            'config',
            'database',
            'public/css/app.css',
            'public/js/app.js',
            'public/index.php',
            'resources',
            'routes',
            'storage/app/public/magic.jpg',
            'tests',
            '.env',
            'artisan',
        ]);

        if (file_exists(self::SERVER . '/current/node_modules')) {
            $this->assertServerHas('node_modules/vendor/package');
            $this->assertServerAssetsAreCompiled();
        }
    }

    public function assertServerHas(...$files)
    {
        $files = is_array($files[0]) ? $files[0] : $files;

        foreach ($files as $file) {
            $this->assertFileExists(self::SERVER . '/current/' . $file);
        }
    }

    public function assertServerMiss(...$files)
    {
        $files = is_array($files[0]) ? $files[0] : $files;

        foreach ($files as $file) {
            $this->assertFileDoesNotExist(self::SERVER . '/current/' . $file);
        }
    }

    public function assertServerFilesEquals($files)
    {
        foreach ($files as $file => $expectedContent) {
            $this->assertStringEqualsFile(
                self::SERVER . '/current/' . $file,
                "$expectedContent\n"
            );
        }
    }

    public function assertServerAssetsAreCompiled()
    {
        $this->assertServerFilesEquals([
            'public/css/app.css' => 'compiled app.css',
            'public/js/app.js' => 'compiled app.js',
        ]);
    }

    public function generatedDeployPath()
    {
        return self::REPOSITORY . '/' . self::GENERATED_DEPLOY_PATH;
    }
}
