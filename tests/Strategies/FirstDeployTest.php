<?php

namespace Lorisleiva\LaravelDeployer\Test\Strategies;

use Lorisleiva\LaravelDeployer\Test\DeploymentTestCase;

class FirstDeployTest extends DeploymentTestCase
{
    protected $configs = 'basic';

    /** @test */
    function firstdeploy_shared_copies_items_from_rootpath_if_exists()
    {
        // Given we have a text file in the storage of our rootpath.
        $this->runInRoot('mkdir -p storage/app/public');
        $this->runInRoot('touch storage/app/public/foobar.txt');

        // And we have a .env file in out rootpath but ignored in our repository.
        $this->runInRoot('touch .env');
        $this->runInRoot('echo "APP_NAME=FooBar" > .env');

        // When we deploy.
        $this->artisan('deploy', ['-s' => 'firstdeploy']);

        // Then the text file has been added to our shared folder.
        $this->assertFileExists(self::SERVER . '/shared/storage/app/public/foobar.txt');

        // And the .env file comes from our rootpath.
        $this->assertServerFilesEquals(['.env' => 'APP_NAME=FooBar']);
    }

    /** @test */
    function in_case_of_conflict_it_takes_items_from_the_release_path()
    {
        // Given we have a `magic.jpg` file on the rootpath
        $this->runInRoot('mkdir -p storage/app/public');
        $this->runInRoot('touch storage/app/public/magic.jpg');
        $this->assertFileExists(self::SERVER . '/storage/app/public/magic.jpg');

        // And a `magic.jpg` file on the repository itself.
        $this->assertFileExists(self::REPOSITORY . '/storage/app/public/magic.jpg');

        // When we deploy.
        $this->artisan('deploy', ['-s' => 'firstdeploy']);

        // Then the `magic.jpg` file in the shared folder comes from the repository.
        $this->assertFileExists(self::SERVER . '/shared/storage/app/public/magic.jpg');
        $this->assertFileEquals(
            self::REPOSITORY . '/storage/app/public/magic.jpg', 
            self::SERVER . '/shared/storage/app/public/magic.jpg'
        );
    }

    /** @test */
    function firstdeploy_cleanup_should_get_rid_of_everything_in_the_rootpath_that_isnt_part_of_deployer()
    {
        // Given we already have a release of our app in the rootpath.
        $this->runInRoot('cp -r ' . self::REPOSITORY . '/* .');
        $this->runInRoot('touch .env');
        $this->assertFileExists(self::SERVER . '/.env');
        $this->assertDirectoryExists(self::SERVER . '/app');
        $this->assertDirectoryExists(self::SERVER . '/bootstrap');
        $this->assertDirectoryExists(self::SERVER . '/config');
        $this->assertDirectoryExists(self::SERVER . '/database');
        $this->assertDirectoryExists(self::SERVER . '/public');

        // When we deploy and then cleanup.
        $this->artisan('deploy', ['-s' => 'firstdeploy']);

        // Then we are only left with the 4 Deployer folders.
        $this->assertEquals(4, $this->runInRoot("ls -A1 . | wc -l"));
        $this->assertDirectoryExists(self::SERVER . '/current');
        $this->assertDirectoryExists(self::SERVER . '/releases');
        $this->assertDirectoryExists(self::SERVER . '/shared');
        $this->assertDirectoryExists(self::SERVER . '/.dep');
    }
}