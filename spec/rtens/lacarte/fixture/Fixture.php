<?php
namespace spec\rtens\lacarte\fixture;

use rtens\mockster\MockFactory;
use spec\rtens\lacarte\TestCase;
use watoki\factory\Factory;

class Fixture {

    /** @var Factory */
    protected $factory;

    /** @var TestCase */
    protected $test;

    public function __construct(TestCase $test, Factory $factory) {
        $this->test = $test;
        $this->factory = $factory;
        $this->mockFactory = new MockFactory();
    }

    public function setUp() {}

    public function tearDown() {}

}