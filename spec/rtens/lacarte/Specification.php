<?php
namespace spec\rtens\lacarte;

use rtens\lacarte\core\Configuration;
use rtens\mockster\MockFactory;
use watoki\stepper\Migrater;

abstract class Specification extends \watoki\scrut\Specification {

    public static $CLASS = __CLASS__;

    /** @var MockFactory */
    public $mockFactory;

    protected function setUp() {
        $this->mockFactory = new MockFactory();
        parent::setUp();
    }

    protected function loadDependencies() {

        $userFilesDir = __DIR__ . '/__userfiles';
        $this->cleanUp($userFilesDir);
        @mkdir($userFilesDir);

        $stateFile = $userFilesDir . '/migration' . uniqid();

        $config = $this->mockFactory->getInstance(Configuration::Configuration);
        $config->__mock()->method('getPdoDataSourceName')->willReturn('sqlite::memory:');
        $config->__mock()->method('getHost')->willReturn('http://lacarte');
        $config->__mock()->method('getUserFilesDirectory')->willReturn($userFilesDir);
        $this->factory->setSingleton(Configuration::Configuration, $config);

        if (file_exists($stateFile))
            unlink($stateFile);

        $that = $this;
        $this->undos[] = function () use ($that, $stateFile, $userFilesDir) {
            unlink($stateFile);
            $that->cleanUp($userFilesDir);
        };

        $this->migrate($stateFile);

        parent::loadDependencies();
    }

    private function migrate($stateFile) {
        $migrater = new Migrater($this->factory, 'rtens\lacarte\model\migration', $stateFile);
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