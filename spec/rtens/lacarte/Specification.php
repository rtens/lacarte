<?php
namespace spec\rtens\lacarte;

use rtens\lacarte\core\Configuration;
use rtens\lacarte\model\migration\Step1;
use rtens\mockster\MockFactory;
use watoki\stepper\Migrater;

abstract class Specification extends \watoki\scrut\Specification {

    public static $CLASS = __CLASS__;

    /** @var MockFactory */
    public $mockFactory;

    protected function loadDependencies() {
        $this->mockFactory = new MockFactory();

        $userFilesDir = __DIR__ . '/__userfiles';
        $this->cleanUp($userFilesDir);
        @mkdir($userFilesDir);

        $config = $this->mockFactory->getInstance(Configuration::$CLASS);
        $config->__mock()->method('getPdoDataSourceName')->willReturn('sqlite::memory:');
        $config->__mock()->method('getHost')->willReturn('http://lacarte');
        $config->__mock()->method('getUserFilesDirectory')->willReturn($userFilesDir);
        $this->factory->setSingleton(Configuration::$CLASS, $config);

        $that = $this;
        $this->undos[] = function () use ($that, $userFilesDir) {
            $that->cleanUp($userFilesDir);
        };

        $this->migrate();

        parent::loadDependencies();
    }

    private function migrate() {
        $migrater = new Migrater($this->factory->getInstance(Step1::$CLASS));
        $migrater->migrate();
    }

    public function cleanUp($dir) {
        foreach (glob($dir . '/*') as $file) {
            if (is_dir($file)) {
                $this->cleanUp($file);
            } else {
                @unlink($file);
            }
        }
        @rmdir($dir);
    }

}