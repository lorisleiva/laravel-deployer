<?php

namespace Deployer;

desc('Copying the shared folder from the deploy path if possible');
task('firstdeploy:shared', function () {
    $sharedPath = "{{deploy_path}}/shared";

    foreach (get('shared_dirs') as $dir) {
        if (test("[ -d {{deploy_path}}/$dir ]")) {
            run("mkdir -p $sharedPath/$dir");
            run("rsync -r --ignore-existing {{deploy_path}}/$dir $sharedPath/" . dirname(parse($dir)));
        }
    }

    foreach (get('shared_files') as $file) {
        if (test("[ -f {{deploy_path}}/$file ]")) {
            run("mkdir -p $sharedPath/" . dirname(parse($file)));
            run("rsync -r --ignore-existing {{deploy_path}}/$file $sharedPath/$file");
        }
    }
});

desc('Deletes everything from deploy path that isn\'t from deployer');
task('firstdeploy:cleanup', function () {
    $filesToKeep = '.dep\|current\|release\|releases\|shared';
    $filesToDelete = run("echo `ls {{deploy_path}} | grep -v '$filesToKeep'`");

    writeln("<info>Make sure that \"{{deploy_path}}/current\" is your new server root path!</info>");
    
    $question = "Are you sure you want to delete the following elements?\n$filesToDelete";
    if (get('debug', false) || askConfirmation($question, false)) {
        run("cd {{deploy_path}} && rm -rf $filesToDelete");
    }
});