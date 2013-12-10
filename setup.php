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

if (!file_exists('build/composer.phar')) {
    echo "Downloading Composer installer..." . PHP_EOL;
    @mkdir('build');
    file_put_contents("build/install_composer.php", file_get_contents('http://getcomposer.org/installer'));

    echo "Installing composer.phar" . PHP_EOL;
    system("php build/install_composer.php --install-dir build");
    system("php build/composer.phar install --dev");
}

/** @var \watoki\factory\Factory $factory */
$factory = require_once 'bootstrap.php';

$command = new DependentCommandGroup();
$app = new CliApplication($command);;

$command->add('install-dependencies', GenericCommand::build(function () {
    system("php build/composer.phar install --dev");
})->setDescription('Installs dependencies of the project into "vendor" directory.'));

$command->add('update-dependencies', GenericCommand::build(function () {
    system(shell_exec("php build/composer.phar update"));
})->setDescription('Updates the dependencies of the project.'));

$command->add('migrate', new StepperCommand($factory->getInstance(Step1::$CLASS), __DIR__ . '/usr/migration.state'));

$command->add('config', new CreateUserConfigurationCommand(Configuration::$CLASS, $configFile, $factory));

$command->add('install', GenericCommand::build(function (Console $console) {
    if (!file_exists('.htaccess')) {
        $console->out->writeLine("Copying .htaccess");
        copy('.htaccess.dist', '.htaccess');
    }
})->setDescription('Installs the project'));

$command->add('test', GenericCommand::build(function () {
    system("php vendor/phpunit/phpunit/phpunit.php");
})->setDescription('Runs the test suite.'));

$command->add('build', GenericCommand::build()->setDescription('Builds project for new deployment'));

$command->addDependency('build', 'install-dependencies');
$command->addDependency('build', 'migrate');

$command->addDependency('config', 'build');

$command->addDependency('install', 'build');
$command->addDependency('test', 'build');

$app->run();
