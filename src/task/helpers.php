<?php

namespace Deployer;

/**
 * Run an artisan command.
 *
 * Supported options:
 * - 'min' => #.#: The minimum Laravel version required (included).
 * - 'max' => #.#: The maximum Laravel version required (included).
 * - 'skipIfNoEnv': Skip and warn the user if `.env` file is inexistant or empty.
 * - 'failIfNoEnv': Fail the command if `.env` file is inexistant or empty.
 * - 'runInCurrent': Run the artisan command in the current directory.
 * - 'showOutput': Show the output of the command if given.
 *
 * @param string $command The artisan command (with cli options if any).
 * @param array $options The options that define the behaviour of the command.
 * @return callable A function that can be used as a task.
 */
function artisan($command, $options = []) 
{
    return function() use ($command, $options) {

        $versionTooEarly = array_key_exists('min', $options) 
            && laravel_version_compare($options['min'], '<');

        $versionTooLate = array_key_exists('max', $options) 
            && laravel_version_compare($options['max'], '>');

        if ($versionTooEarly || $versionTooLate) {
            return;
        }

        if (in_array('failIfNoEnv', $options) && ! test('[ -s {{release_path}}/.env ]')) {
            throw new \Exception('Your .env file is empty! Cannot proceed.');
        }

        if (in_array('skipIfNoEnv', $options) && ! test('[ -s {{release_path}}/.env ]')) {
            writeln("<fg=yellow;options=bold;>Warning: </><fg=yellow;>Your .env file is empty! Skipping...</>");
            return;
        }

        $artisan = in_array('runInCurrent', $options) 
            ? '{{deploy_path}}/current/artisan' 
            : '{{release_path}}/artisan';

        $output = run("{{bin/php}} $artisan $command");
        
        if (in_array('showOutput', $options)) {
            writeln("<info>$output</info>");
        }
    };
}

function laravel_version_compare($version, $comparator)
{
    return version_compare(get('laravel_version'), $version, $comparator);
}

function copyShared($from, $to)
{
    foreach (get('shared_dirs') as $dir) {
        if (test("[ -d $from/$dir ]")) {
            run("mkdir -p $to/$dir");
            run("rsync -r --ignore-existing $from/$dir $to/" . dirname(parse($dir)));
        }
    }
    foreach (get('shared_files') as $file) {
        if (test("[ -f $from/$file ]")) {
            run("mkdir -p $to/" . dirname(parse($file)));
            run("rsync --ignore-existing $from/$file $to/$file");
        }
    }
}
