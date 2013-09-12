<?php
namespace spec\rtens\lacarte\fixtures\service;

use rtens\lacarte\core\Configuration;
use rtens\lacarte\core\FileRepository;
use rtens\mockster\Mock;
use rtens\mockster\MockFactory;
use spec\rtens\lacarte\fixtures\model\UserFixture;
use spec\rtens\lacarte\TestCase;
use watoki\factory\Factory;
use watoki\scrut\Fixture;

class FileFixture extends Fixture {

    public static $CLASS = __CLASS__;

    /** @var UserFixture */
    private $user;

    /** @var FileRepository|Mock */
    private $file;

    public function __construct(TestCase $test, Factory $factory, Configuration $config) {
        parent::__construct($test, $factory);
        $this->user = $test->useFixture(UserFixture::$CLASS);
        $this->config = $config;

        $this->file = $test->mockFactory->createTestUnit(FileRepository::$CLASS, array(
            'config' => $config
        ));
        $factory->setSingleton(FileRepository::$CLASS, $this->file);

        $this->file->__mock()->method('moveUploadedFile')->willCall(function ($from, $to) {
            return rename($from, $to);
        });
    }

    public function given_HasAnAvatar($userName) {
        $this->givenTheFile($this->getAvatarFile($userName));
    }

    public function then_ShouldHaveAnAvatar($userName) {
        $filename = $this->getAvatarFile($userName);
        $this->test->assertTrue($this->file->exists($filename), "File [$filename] does not exist.");
    }

    private function getAvatarFile($userName) {
        return 'avatars/' . $this->user->getUser($userName)->id . '.jpg';
    }

    public function givenTheFile($filename) {
        $path = $this->file->getFullPath($filename);
        @mkdir(dirname($path), 0777, true);
        file_put_contents($path, 'n');
    }

    public function getFullPath($fileName) {
        return $this->file->getFullPath($fileName);
    }

}