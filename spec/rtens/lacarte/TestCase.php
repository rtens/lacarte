<?php
namespace spec\rtens\lacarte;

use rtens\lacarte\core\Configuration;
use rtens\mockster\MockFactory;
use watoki\factory\Factory;
use watoki\stepper\Migrater;

abstract class TestCase extends \PHPUnit_Framework_TestCase {

    public static $CLASS = __CLASS__;

    /** @var Factory */
    private $factory;

    private $undos = array();

    protected function setUp() {
        parent::setUp();

        $this->factory = new Factory();
        $this->factory->setSingleton(TestCase::$CLASS, $this);

        $mf = new MockFactory();

        $userFilesDir = __DIR__ . '/__userfiles';
        $this->cleanUp($userFilesDir);
        mkdir($userFilesDir);

        $stateFile = $userFilesDir . '/migration' . uniqid();

        $config = $mf->createMock(Configuration::Configuration);
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

    protected function tearDown() {
        foreach ($this->undos as $undo) {
            $undo();
        }
        parent::tearDown();
    }

    protected function useFixture($class) {
        return $this->factory->getInstance($class);
    }

}