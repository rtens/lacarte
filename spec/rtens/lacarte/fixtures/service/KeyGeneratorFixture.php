<?php
namespace spec\rtens\lacarte\fixtures\service;

use rtens\lacarte\utils\KeyGenerator;
use rtens\mockster\MockFactory;
use spec\rtens\lacarte\Specification;
use watoki\factory\Factory;
use watoki\scrut\Fixture;

class KeyGeneratorFixture extends Fixture {

    public static $CLASS = __CLASS__;

    public function __construct(Specification $spec, Factory $factory) {
        parent::__construct($spec, $factory);

        $mf = new MockFactory();
        $this->keyGenerator = $mf->getInstance(KeyGenerator::$CLASS);
        $factory->setSingleton(KeyGenerator::$CLASS, $this->keyGenerator);
    }

    public function givenTheNextGeneratedKeyIs($key) {
        $this->keyGenerator->__mock()->method('generateUnique')->willReturn($key)->once();
    }

}