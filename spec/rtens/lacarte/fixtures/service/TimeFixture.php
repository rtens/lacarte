<?php
namespace spec\rtens\lacarte\fixtures\service;

use rtens\lacarte\utils\TimeService;
use rtens\mockster\MockFactory;
use spec\rtens\lacarte\TestCase;
use watoki\factory\Factory;
use watoki\scrut\Fixture;

class TimeFixture extends Fixture {

    public static $CLASS = __CLASS__;

    public function __construct(TestCase $test, Factory $factory) {
        parent::__construct($test, $factory);

        $this->time = $test->mockFactory->createTestUnit(TimeService::$CLASS);
        $factory->setSingleton(TimeService::$CLASS, $this->time);
    }

    public function givenNowIs($string) {
        $this->time->__mock()->method('now')->willCall(function () use ($string) {
            return new \DateTime($string);
        });
    }

}