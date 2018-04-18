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