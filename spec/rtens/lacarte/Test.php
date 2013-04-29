<?php
namespace spec\rtens\lacarte;

use rtens\lacarte\core\Configuration;
use rtens\lacarte\core\Database;
use rtens\mockster\Mock;
use rtens\mockster\MockFactory;
use watoki\factory\Factory;
use watoki\stepper\Migrater;

/**
 * @property Test_Given given
 * @property Test_When when
 * @property Test_Then then
 */
abstract class Test extends \PHPUnit_Framework_TestCase {

    /**
     * @var Factory
     */
    public $factory;

    /**
     * @var MockFactory
     */
    public $mf;

    /** @var Mock */
    public $config;

    private $stateFile;

    public function setUp() {
        parent::setUp();

        $this->stateFile = __DIR__ . '/migration' . uniqid();

        $this->mf = new MockFactory();

        $this->config = $this->mf->createMock(Configuration::Configuration);
        $this->config->__mock()->method('getPdoDataSourceName')->willReturn('sqlite::memory:');
        $this->config->__mock()->method('getHost')->willReturn('http://lacarte');

        $this->factory = new Factory();
        $this->factory->setSingleton(Configuration::Configuration, $this->config);

        if (file_exists($this->stateFile)) unlink($this->stateFile);
        $this->migrate();

        $this->createSteps();
    }

    public function tearDown() {
        unlink($this->stateFile);
        parent::tearDown();
    }

    private function migrate() {
        $migrater = new Migrater($this->factory, 'rtens\lacarte\model\migration', $this->stateFile);
        $migrater->migrate();
    }

    private function createSteps() {
        foreach (array('given', 'when', 'then') as $steps) {
            $class = get_class($this);
            while ($class) {
                $stepClass = $class . '_' . ucfirst($steps);
                if (class_exists($stepClass)) {
                    $this->$steps = $this->factory->getInstance($stepClass, array('test' => $this));
                    break;
                }
                $refl = new \ReflectionClass($class);
                $class = $refl->getParentClass()->getName();
            }
        }
    }

}

/**
 * @property Test test
 */
class Test_Given {

    function __construct(Test $test) {
        $this->test = $test;
    }

}

/**
 * @property Test test
 */
class Test_When {

    /**
     * @var null|\Exception
     */
    public $caught;

    function __construct(Test $test) {
        $this->test = $test;
    }

}

/**
 * @property Test test
 */
class Test_Then {

    function __construct(Test $test) {
        $this->test = $test;
    }

    protected function getFieldIn($string, $field) {
        foreach (explode('/', $string) as $key) {
            if (!array_key_exists($key, $field)) {
                throw new \Exception("Could not find '$key' in " . json_encode($field));
            }
            $field = $field[$key];
        }
        return $field;
    }

    public function anExceptionShouldBeThrownContaining($msg) {
        $this->test->assertNotNull($this->test->when->caught, 'No exception was thrown');
        $this->test->assertContains($msg, $this->test->when->caught->getMessage());
    }

}
