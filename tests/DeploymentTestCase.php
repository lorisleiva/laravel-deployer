<?php

namespace Lorisleiva\LaravelDeployer\Test;

class DeploymentTestCase extends TestCase
{
    const TMP = __DIR__ . '/fixtures/tmp';
    const REPOSITORY = __DIR__ . '/fixtures/tmp/repository';
    const SERVER = __DIR__ . '/fixtures/tmp/server';

    protected $recipe = 'basic';

    protected function getEnvironmentSetUp($app)
    {
        $app->setBasePath(static::REPOSITORY);
    }

    public function setUp()
    {
        parent::setUp();
        $this->resetTempDirectory();
    }

    public function tearDown()
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
    }

    public function createRepository()
    {
        // Copy from static repository base.
        $this->exec('rsync -r ' . static::BASE_REPOSITORY . '/ ' . static::REPOSITORY);

        // Add symlink to Deployer binary.
        $this->runInRepository('mkdir -p vendor/bin');
        $this->runInRepository('ln -s ' . __DIR__ . '/../vendor/bin/dep vendor/bin/dep');

        // Add deploy.php file.
        if ($recipeFile = realpath(static::RECIPES . '/' . $this->recipe . '.php')) {
            $this->runInRepository("cp $recipeFile deploy.php");
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

    public function runInRepository($command)
    {
        return $this->exec("cd " . static::REPOSITORY . " && $command");
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
            'storage/app/public/magic.gif',
            'tests',
            '.env',
            'artisan',
        ]);

        if (file_exists(self::SERVER . '/current/node_modules')) {
            $this->assertServerHas([ 'node_modules/vendor/package' ]);
            $this->assertServerFilesEquals([
                'public/css/app.css' => 'compiled app.css',
                'public/js/app.js' => 'compiled app.js',
            ]);
        }
    }

    public function assertServerHas($files)
    {
        foreach ($files as $file) {
            $this->assertFileExists(self::SERVER . '/current/' . $file);
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
}