<?php
namespace spec\rtens\lacarte\fixtures\service;

use rtens\lacarte\utils\KeyGenerator;
use rtens\mockster\MockFactory;
use spec\rtens\lacarte\TestCase;
use watoki\factory\Factory;
use watoki\scrut\Fixture;

class KeyGeneratorFixture extends Fixture {

    public static $CLASS = __CLASS__;

    public function __construct(TestCase $test, Factory $factory) {
        parent::__construct($test, $factory);

        $mf = new MockFactory();
        $this->keyGenerator = $mf->createMock(KeyGenerator::$CLASS);
        $factory->setSingleton(KeyGenerator::$CLASS, $this->keyGenerator);
    }

    public function givenTheNextGeneratedKeyIs($key) {
        $this->keyGenerator->__mock()->method('generateUnique')->willReturn($key)->once();
    }

}