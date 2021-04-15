<?php

namespace Deployer;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputOption;

desc('Run "php artisan my-command" on the host.');
task('artisan:my-command', function () {
    cd('{{release_or_current_path}}');
    run('php artisan my-command');
});

desc('Show the content of the app directory.');
task('app:directory', function () {
    cd('{{release_or_current_path}}');
    $output = run('ls -la');
    writeln($output);
});

desc('Demo all writing functions.');
task('app:output', function () {
    writeln('This is a line written using "writeln".');
    info('This is a line written using "info".');
    warning('This is a line written using "warning".');

    $output = output();
    $progressBar = new ProgressBar($output, 100);
    $progressBar->setProgress(60);
});

desc('Demo all prompting functions.');
task('app:input', function () {
    $fruit = ask("What's your favourite fruit?", 'strawberry');
    writeln("You're favourite fruit is: $fruit");

    $diet = askChoice("What's your diet?", ['Vegan', 'Vegeterian', 'Pescatarian', 'Carnivore'], 0);
    writeln("You're diet is: $diet");

    $likesCooking = askConfirmation("Do you like cooking?", true);
    writeln($likesCooking ? "Hell yeah! Let's cook together!" : "No worries, I'll cook for you!");

    $secretIngredient = askHiddenResponse("What's your secret ingredient?");
    writeln("Your secret ingredient is safe with me.");
});

set('should_prompt', true);

desc('Demo bypassing prompts.');
task('app:bypass:prompt', function () {
    $shouldPrompt = get('should_prompt', true);
    $fruit = $shouldPrompt ? ask("What's your favourite fruit?", 'strawberry') : 'strawberry';
    writeln("You're favourite fruit is: $fruit");
});
