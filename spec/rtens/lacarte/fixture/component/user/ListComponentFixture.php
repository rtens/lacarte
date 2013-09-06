<?php
namespace spec\rtens\lacarte\fixture\component\user;

use rtens\lacarte\web\user\ListComponent;
use rtens\mockster\MockFactory;
use spec\rtens\lacarte\fixture\component\ComponentFixture;

/**
 * @property ListComponent $component
 * @property ListComponent $component
 */
class ListComponentFixture extends ComponentFixture {

    public static $CLASS = __CLASS__;

    private $newName;

    private $newEmail;

    public function givenIHaveEnteredTheName($string) {
        $this->newName = $string;
    }

    public function givenIHaveEnteredTheEmail($string) {
        $this->newEmail = $string;
    }

    public function whenICreateANewUser() {
        $this->model = $this->component->doPost($this->newName, $this->newEmail);
    }

    public function whenIAccessTheUserList() {
        $this->model = $this->component->doGet();
    }

    public function whenIDeleteTheUser($name) {
        $this->model = $this->component->doDelete($this->user->getUser($name)->id);
    }

    public function thenTheSuccessMessageShouldBe($string) {
        $this->test->assertEquals($string, $this->getField('success'));
    }

    public function thenTheErrorMessageShouldBe($string) {
        $this->test->assertEquals($string, $this->getField('error'));
    }

    public function thenTheNewNameFieldShouldContain($string) {
        $this->test->assertEquals($string, $this->getField('name/value'));
    }

    public function thenTheEmailFieldShouldContain($string) {
        $this->test->assertEquals($string, $this->getField('email/value'));
    }

    public function thenTheUserListShouldBeEmpty() {
        $this->test->assertCount(0, $this->getField('user'));
    }

    public function thenThereShouldBe_Users($count) {
        $this->test->assertCount($count, $this->getField('user'));
    }

    public function thenTheAvatarOfUserAtPosition_ShouldBe($position, $imgSrc) {
        $i = $position - 1;
        $this->test->assertEquals($imgSrc, $this->getField("user/$i/avatar/src"));
    }

    protected function getComponentClass() {
        return ListComponent::$CLASS;
    }
}