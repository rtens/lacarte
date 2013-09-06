<?php
namespace spec\rtens\lacarte\fixture\model;

use rtens\lacarte\core\Configuration;
use rtens\mockster\MockFactory;
use spec\rtens\lacarte\fixture\Fixture;
use spec\rtens\lacarte\TestCase;
use watoki\factory\Factory;

class FileFixture extends Fixture {

    public static $CLASS = __CLASS__;

    public function __construct(TestCase $test, Factory $factory, UserFixture $user, Configuration $config) {
        parent::__construct($test, $factory);
        $this->user = $user;
        $this->config = $config;
    }

    public function given_HasAnAvatar($userName) {
        $dir = $this->config->getUserFilesDirectory() . '/avatars';
        @mkdir($dir);

        $file = $dir . '/' . $this->user->getUser($userName)->id . '.jpg';
        file_put_contents($file, 'n');
    }

}