<?php

echo "Downloading Composer installer..." . PHP_EOL;
mkdir('build');
file_put_contents("build/composer_installer.php", file_get_contents('http://getcomposer.org/installer'));

echo "Installing composer.phar" . PHP_EOL;
echo shell_exec("php build/composer_installer.php --install-dir build");

echo "Installing dependencies" . PHP_EOL;
echo shell_exec("php build/composer.phar install --dev");

if (!file_exists('config/UserConfiguration.php')) {
	echo "Copying configuration" . PHP_EOL;
	copy('config/UserConfiguration.php.dist', 'config/UserConfiguration.php');
}

echo "Setting-up database" . PHP_EOL;
@mkdir('opt');
echo shell_exec('php vendor/watoki/stepper/bin/stepper.php migrate');

echo 'Done' . PHP_EOL;