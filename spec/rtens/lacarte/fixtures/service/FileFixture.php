<?php
namespace spec\rtens\lacarte\fixtures\service;

use rtens\lacarte\core\Configuration;
use rtens\lacarte\core\FileRepository;
use rtens\mockster\Mock;
use rtens\mockster\Mockster;
use spec\rtens\lacarte\fixtures\model\UserFixture;
use spec\rtens\lacarte\Specification;
use watoki\factory\Factory;
use watoki\scrut\Fixture;

/**
 * @property UserFixture user <-
 * @property UserFixture user
 */
class FileFixture extends Fixture {

    public static $CLASS = __CLASS__;

    /** @var FileRepository|Mock */
    private $file;

    public function __construct(Specification $spec, Factory $factory, Configuration $config) {
        parent::__construct($spec, $factory);
        $this->config = $config;

        $this->file = $spec->mockFactory->getInstance(FileRepository::$CLASS, array(
            'config' => $config
        ));
        $factory->setSingleton(FileRepository::$CLASS, $this->file);

        $this->file->__mock()->mockMethods(Mockster::F_NONE);
        $this->file->__mock()->method('moveUploadedFile')->willCall(function ($from, $to) {
            return rename($from, $to);
        });
    }

    public function given_HasAnAvatar($userName) {
        $this->givenTheFile($this->getAvatarFile($userName));
    }

    public function then_ShouldHaveAnAvatar($userName) {
        $filename = $this->getAvatarFile($userName);
        $this->spec->assertTrue($this->file->exists($filename), "File [$filename] does not exist.");
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