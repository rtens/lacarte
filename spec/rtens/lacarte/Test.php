<?php
namespace spec\rtens\lacarte;

use rtens\lacarte\core\Database;
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

    private $stateFile;

    protected function setUp() {
        parent::setUp();

        $this->stateFile = __DIR__ . '/migration';

        $this->factory = new Factory();
        $this->factory->getInstance(Database::$CLASS, array(
            'pdo' => new \PDO('sqlite::memory:')
        ));

        if (file_exists($this->stateFile)) unlink($this->stateFile);
        $this->migrate();

        $this->createSteps();
    }

    protected function tearDown() {
        unlink($this->stateFile);
        parent::tearDown();
    }

    private function migrate() {
        $migrater = new Migrater($this->factory, 'rtens\lacarte\model\migration', $this->stateFile);
        $migrater->migrate();
    }

    private function createSteps() {
        foreach (array('given', 'when', 'then') as $steps) {
            $class = get_class($this) . '_' . ucfirst($steps);
            if (class_exists($class)) {
                $this->$steps = new $class($this);
            } else {
                $class = 'spec\rtens\lacarte_' . ucfirst($steps);
                $this->$steps = new $class($this);
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

}
