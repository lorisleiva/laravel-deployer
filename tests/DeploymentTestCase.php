<?php

namespace Lorisleiva\LaravelDeployer\Test;

class DeploymentTestCase extends TestCase
{
    const REPOSITORY = __DIR__ . '/fixtures/repository';
    const RECIPES = __DIR__ . '/fixtures/recipes';
    const TMP = __DIR__ . '/fixtures/tmp';

    protected $recipe = 'basic';

    public function setUp()
    {
        parent::setUp();
        $this->resetTempDirectory();
        $this->resetRepository();
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->exec('rm -rf ' . static::TMP);
        $this->exec('rm -rf ' . static::REPOSITORY . '/.git');
        $this->exec('rm -f ' . static::REPOSITORY . '/deploy.php');
    }

    public function resetTempDirectory()
    {
        $this->exec('rm -rf ' . static::TMP);
        mkdir(static::TMP);
    }

    public function resetRepository()
    {
        $this->resetDeployFile();

        $this->exec('rm -rf ' . static::REPOSITORY . '/.git');
        $this->runInRepository("git init");
        $this->runInRepository("git add .");
        $this->runInRepository("git config user.name 'John Smith'");
        $this->runInRepository("git config user.email 'john.smith@example.com'");
        $this->runInRepository("git commit -m 'init commit'");
    }

    public function resetDeployFile()
    {
        $recipeFile = realpath(static::RECIPES . '/' . $this->recipe . '.php');
        $deployFile = static::REPOSITORY . '/deploy.php';

        $this->exec("rm -f $deployFile");

        if ($recipeFile) {
            $this->exec("cp $recipeFile $deployFile");
        }
    }

    public function runInRepository($command)
    {
        return $this->exec("cd " . static::REPOSITORY . " && $command");
    }

    public function runInRoot($command)
    {
        return $this->exec("cd " . static::TMP . " && $command");
    }

    public function runInCurrent($command)
    {
        return $this->exec("cd " . static::TMP . "/current && $command");
    }
}