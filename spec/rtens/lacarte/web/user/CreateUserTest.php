<?php
namespace spec\rtens\lacarte\web\user;

use spec\rtens\lacarte\Test;
use spec\rtens\lacarte\Test_Given;
use spec\rtens\lacarte\Test_When;
use spec\rtens\lacarte\Test_Then;

/**
 * @property CreateUserTest_Given given
 * @property CreateUserTest_When when
 * @property CreateUserTest_Then then
 */
class CreateUserTest extends Test {

    function testSuccess() {
        $this->markTestIncomplete();
    }

    function testNotAdmin() {
        $this->markTestIncomplete();
    }

    function testEmptyName() {
        $this->given->iEnteredTheEmail('some@mail.com');

        $this->when->iCreateANewUser();

        $this->then->thereShouldBeAnError('name');
    }

    function testEmptyEmail() {
        $this->given->iEnteredTheName('Some Name');

        $this->when->iCreateANewUser();

        $this->then->thereShouldBeAnError('name');
    }

    function testAlreadyExistingEmail() {
        $this->given->theExistingUser('Peter', 'peter@parker.com', 'noKey');
        $this->given->iEnteredTheName('Spider Man');
        $this->given->iEnteredTheEmail('peter@parker.com');

        $this->when->iCreateANewUser();

        $this->then->thereShouldBeAnError('exist');
    }

}

class CreateUserTest_Given extends Test_Given {

    public function iEnteredTheEmail($string) {
        $this->test->markTestIncomplete();
    }

    public function iEnteredTheName($string) {
        $this->test->markTestIncomplete();
    }

    public function theExistingUser($name, $email) {
        $this->test->markTestIncomplete();
    }
}

class CreateUserTest_When extends Test_When {

    public function iCreateANewUser() {
        $this->test->markTestIncomplete();
    }
}

class CreateUserTest_Then extends Test_Then {

    public function thereShouldBeAnError($msg) {
        $this->test->markTestIncomplete();
    }
}