<?php
namespace spec\rtens\lacarte\fixture\component;

use rtens\lacarte\web\LaCarteModule;
use rtens\lacarte\web\user\ListComponent;
use spec\rtens\lacarte\fixture\Fixture;

class UserComponentFixture extends Fixture {

    public static $CLASS = __CLASS__;

    public $newName;

    public $newEmail;

    /** @var ListComponent */
    public $component;

    public $model;

    public function setUp() {
        parent::setUp();

        $root = $this->factory->getInstance(LaCarteModule::$CLASS);
        $this->component = $this->factory->getInstance(ListComponent::$CLASS, array(
            'parent' => $root
        ));
    }

    public function givenIHaveEnteredTheName($string) {
        $this->newName = $string;
    }

    public function givenIHaveEnteredTheEmail($string) {
        $this->newEmail = $string;
    }

    public function whenICreateANewUser() {
        $this->model = $this->component->doPost($this->newName, $this->newEmail);
    }

    public function thenTheSuccessMessageShouldBe($string) {
        $this->test->assertEquals($string, $this->model['success']);
    }
}