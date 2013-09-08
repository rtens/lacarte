<?php
namespace spec\rtens\lacarte\fixtures;

use rtens\mockster\MockFactory;
use spec\rtens\lacarte\TestCase;
use watoki\factory\Factory;

class Fixture {

    /** @var TestCase */
    protected $test;

    public function __construct(TestCase $test, Factory $factory) {
        $factory->setSingleton(get_class($this), $this);

        $this->test = $test;
        $this->mockFactory = new MockFactory();
    }

}