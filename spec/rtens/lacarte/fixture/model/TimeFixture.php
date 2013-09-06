<?php
namespace spec\rtens\lacarte\fixture\model;

use rtens\lacarte\utils\TimeService;
use rtens\mockster\MockFactory;
use spec\rtens\lacarte\fixture\Fixture;
use spec\rtens\lacarte\TestCase;
use watoki\factory\Factory;

class TimeFixture extends Fixture {

    public static $CLASS = __CLASS__;

    public function __construct(TestCase $test, Factory $factory, MockFactory $mf) {
        parent::__construct($test, $factory);

        $this->time = $mf->createMock(TimeService::$CLASS);
        $factory->setSingleton(TimeService::$CLASS, $this->time);
    }

    public function givenNowIs($string) {
        $this->time->__mock()->method('now')->willCall(function () use ($string) {
            return new \DateTime($string);
        });
    }

}