<?php
namespace spec\rtens\lacarte\web\user;

use rtens\lacarte\UserInteractor;
use rtens\lacarte\web\user\ListComponent;
use spec\rtens\lacarte\Test;
use spec\rtens\lacarte\web\ComponentTest;
use spec\rtens\lacarte\web\ComponentTest_Given;
use spec\rtens\lacarte\web\ComponentTest_Then;
use spec\rtens\lacarte\web\ComponentTest_When;
use watoki\curir\Path;

/**
 * @property CreateUserTest_Given given
 * @property CreateUserTest_When when
 * @property CreateUserTest_Then then
 */
class CreateUserTest extends ComponentTest {

    function testSuccess() {
        $this->given->iAmLoggedIn();
        $this->given->iAmAdmin();
        $this->given->iEnteredTheName('A Name');
        $this->given->iEnteredTheEmail('some@example.com');

        $this->when->iCreateANewUser();

        $this->then->aUserWithName_AndEmail_ShouldBeCreated('A Name', 'some@example.com');
        $this->then->_shouldBe('success', 'The user A Name was created.');
    }

    function testNotLoggedIn() {
        $this->given->iEnteredTheName('A Name');
        $this->given->iEnteredTheEmail('some@example.com');

        $this->when->iCreateANewUser();

        $this->then->iShouldBeRedirectedTo('login.html');
    }

    function testNotAdmin() {
        $this->given->iAmLoggedIn();
        $this->given->iEnteredTheName('A Name');
        $this->given->iEnteredTheEmail('some@example.com');

        $this->when->iCreateANewUser();

        $this->then->thereShouldBeAnError('Access denied');
    }

    function testError() {
        $this->given->iAmLoggedIn();
        $this->given->iAmAdmin();
        $this->given->anErrorOccurs('Some error');

        $this->when->iCreateANewUser();

        $this->then->thereShouldBeAnError('Some error');
    }

}

/**
 * @property CreateUserTest test
 */
class CreateUserTest_Given extends ComponentTest_Given {

    public $name;
    public $email;

    /** @var \rtens\mockster\Mock|UserInteractor */
    public $userInteractor;

    function __construct(Test $test) {
        parent::__construct($test);
        $this->userInteractor = $test->mf->createMock(UserInteractor::$CLASS);
    }

    public function iEnteredTheName($name) {
        $this->name = $name;
    }

    public function iEnteredTheEmail($email) {
        $this->email = $email;
    }

    public function anErrorOccurs($msg) {
        $this->userInteractor->__mock()->method('createUser')->willThrow(new \Exception($msg));
    }
}

/**
 * @property CreateUserTest test
 */
class CreateUserTest_When extends ComponentTest_When {

    /** @var ListComponent */
    public $component;

    function __construct(Test $test) {
        parent::__construct($test);
        $this->component = $this->test->mf->createTestUnit(ListComponent::$CLASS, array(
            'factory' => $test->factory,
            'route' => new Path(),
            'session' => $this->test->given->session,
            'userInteractor' => $this->test->given->userInteractor
        ));
        $this->component->__mock()->method('subComponent')->setMocked();
    }

    public function iCreateANewUser() {
        $this->model = $this->component->doPost($this->test->given->name, $this->test->given->email);
    }
}

/**
 * @property CreateUserTest test
 */
class CreateUserTest_Then extends ComponentTest_Then {

    public function thereShouldBeAnError($msg) {
        $this->test->assertContains($msg, $this->getField('error'));
    }

    public function aUserWithName_AndEmail_ShouldBeCreated($name, $email) {
        $this->test->assertTrue(
            $this->test->given->userInteractor->__mock()->method('createUser')->wasCalledWith(array(
                $this->test->given->group->id, $name, $email
            )));
    }
}