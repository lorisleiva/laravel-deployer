<?php

namespace Deployer;

function artisan($command, $showOutput = false) {
    return function() use ($command, $showOutput) {
        $output = run("{{bin/php}} {{release_path}}/artisan $command");
        
        if ($showOutput) {
            writeln("<info>$output</info>");
        }
    };
}