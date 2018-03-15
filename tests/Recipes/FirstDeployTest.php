<?php

namespace Lorisleiva\LaravelDeployer\Test\Recipes;

use Lorisleiva\LaravelDeployer\Test\DeploymentTestCase;

class FirstDeployTest extends DeploymentTestCase
{
    protected $recipe = 'basic';

    /** @test */
    function firstdeploy_shared_copies_items_from_rootpath_if_exists()
    {
        // Given we have a text file in the storage of our rootpath.
        $this->runInRoot('mkdir -p storage/app/public');
        $this->runInRoot('touch storage/app/public/foobar.txt');

        // And we have a .env file in out rootpath but ignored in our repository.
        $this->runInRoot('touch .env');
        $this->runInRoot('printf "APP_NAME=FooBar" > .env');

        // When we deploy.
        $this->artisan('deploy');

        // Then the text file has been added to our shared folder.
        $this->assertFileExists(self::SERVER . '/shared/storage/app/public/foobar.txt');

        // And the .env file comes from our rootpath.
        $this->assertStringEqualsFile(self::SERVER . '/.env', 'APP_NAME=FooBar');
    }

    /** @test */
    function in_case_of_conflict_it_takes_items_from_the_release_path()
    {
        // Given we have a `magic.gif` file on the rootpath
        $this->runInRoot('mkdir -p storage/app/public');
        $this->runInRoot('touch storage/app/public/magic.gif');
        $this->assertFileExists(self::SERVER . '/storage/app/public/magic.gif');

        // And a `magic.gif` file on the repository itself.
        $this->assertFileExists(self::REPOSITORY . '/storage/app/public/magic.gif');

        // When we deploy.
        $this->artisan('deploy');

        // Then the `magic.gif` file in the shared folder comes from the repository.
        $this->assertFileExists(self::SERVER . '/shared/storage/app/public/magic.gif');
        $this->assertFileEquals(
            self::REPOSITORY . '/storage/app/public/magic.gif', 
            self::SERVER . '/shared/storage/app/public/magic.gif'
        );
    }

    /** @test */
    function firstdeploy_cleanup_should_get_rid_of_everything_in_the_rootpath_that_isnt_part_of_deployer()
    {
        // Given we already have a release of our app in the rootpath.
        $this->runInRoot('cp -r ' . self::REPOSITORY . '/* .');
        $this->assertDirectoryExists(self::SERVER . '/app');
        $this->assertDirectoryExists(self::SERVER . '/bootstrap');
        $this->assertDirectoryExists(self::SERVER . '/config');
        $this->assertDirectoryExists(self::SERVER . '/database');
        $this->assertDirectoryExists(self::SERVER . '/public');

        // When we deploy and then cleanup.
        $this->artisan('deploy');
        $this->artisan('deploy:run', [
            'task' => 'firstdeploy:cleanup'
        ]);

        // Then we are only left with the `current`, `releases` and `shared` folders.
        $this->assertEquals(3, $this->runInRoot("ls -1 . | wc -l"));
        $this->assertDirectoryExists(self::SERVER . '/current');
        $this->assertDirectoryExists(self::SERVER . '/releases');
        $this->assertDirectoryExists(self::SERVER . '/shared');

        // And the hidden `.dep` folder.
        $this->assertDirectoryExists(self::SERVER . '/.dep');
    }
}