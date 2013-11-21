<?php

use rtens\lacarte\core\Configuration;
use rtens\lacarte\model\migration\Step1;
use watoki\cfg\cli\CreateUserConfigurationCommand;
use watoki\cli\CliApplication;
use watoki\cli\commands\DependentCommandGroup;
use watoki\cli\commands\GenericCommand;
use watoki\cli\Console;
use watoki\stepper\cli\StepperCommand;

@mkdir('usr');

/** @var \watoki\factory\Factory $factory */
$factory = require_once 'bootstrap.php';

$command = new DependentCommandGroup();
$app = new CliApplication($command);

$command->add('composer', GenericCommand::build(function (Console $console) {
    if (!file_exists('build/composer.phar')) {
        $console->out->writeLine("Downloading Composer installer...");
        @mkdir('build');
        file_put_contents("build/install_composer.php", file_get_contents('http://getcomposer.org/installer'));

        $console->out->writeLine("Installing composer.phar");
        $console->out->write(shell_exec(exec('where php') . " build/install_composer.php --install-dir build"));
    } else {
        $console->out->writeLine('Already installed');
    }
})->setDescription('Downloads composer.phar into "build" directory.'));

$command->add('install-dependencies', GenericCommand::build(function (Console $console) {
    $console->out->writeLine("Installing dependencies");
    $console->out->write(shell_exec(exec('where php') . " build/composer.phar install --dev"));
})->setDescription('Installs dependencies of the project into "vendor" directory.'));

$command->add('update-dependencies', GenericCommand::build(function (Console $console) {
    $console->out->writeLine("Updating dependencies");
    $console->out->write(shell_exec(exec('where php') . " build/composer.phar update"));
})->setDescription('Updates the dependencies of the project.'));

$command->add('migrate', new StepperCommand($factory->getInstance(Step1::$CLASS), __DIR__ . '/usr/migration.state'));

$command->add('build', GenericCommand::build()->setDescription('Builds project for new deployment'));

$command->add('config', new CreateUserConfigurationCommand(Configuration::$CLASS, $configFile, $factory));

$command->add('install', GenericCommand::build(function (Console $console) {
    if (!file_exists('.htaccess')) {
        $console->out->writeLine("Copying .htaccess");
        copy('.htaccess.dist', '.htaccess');
    }
})->setDescription('Installs the project'));

$command->add('test', GenericCommand::build(function (Console $console, $verbose = false) {
    $command = exec('where php') . " vendor/phpunit/phpunit/phpunit.php";

    if ($verbose) {
        system($command, $return);
    } else {
        $return = 0;
        $out = array();
        exec($command, $out, $return);

        if ($return) {
            $console->out->writeLine('FAILED!');
            $console->out->writeLine('');
            foreach ($out as $line) {
                $console->out->writeLine($line);
            }
        } else {
            $console->out->writeLine('Passed.');
        }
    }
})->setDescription('Runs the test suite.'));

$command->addDependency('install-dependencies', 'composer');

$command->addDependency('build', 'install-dependencies');
$command->addDependency('build', 'migrate');

$command->addDependency('config', 'build');

$command->addDependency('install', 'build');
$command->addDependency('test', 'build');

$app->run();