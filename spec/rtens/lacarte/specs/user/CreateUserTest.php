<?php
namespace spec\rtens\lacarte\specs\user;

use spec\rtens\lacarte\fixtures\resource\user\ListResourceFixture;
use spec\rtens\lacarte\fixtures\model\UserFixture;
use spec\rtens\lacarte\fixtures\service\KeyGeneratorFixture;
use spec\rtens\lacarte\fixtures\service\SessionFixture;
use spec\rtens\lacarte\Specification;

/**
 * @property SessionFixture session <-
 * @property KeyGeneratorFixture key <-
 * @property UserFixture user <-
 * @property ListResourceFixture component <-
 */
class CreateUserTest extends Specification {

    function testCreateUserSuccessfully() {
        $this->session->givenIAmLoggedInAsAdmin();
        $this->component->givenIHaveEnteredTheName('Bart Simpson');
        $this->component->givenIHaveEnteredTheEmail('bart@simpson.com');
        $this->key->givenTheNextGeneratedKeyIs('key');

        $this->component->whenICreateANewUser();

        $this->component->thenTheSuccessMessageShouldBe('The user Bart Simpson was created.');
        $this->user->thenThereShouldBe_Users(1);
        $this->user->thenThereShouldBeAUserWithTheName_TheEmailAndTheKey('Bart Simpson', 'bart@simpson.com', 'key');
    }

    function testNotLoggedIn() {
        $this->component->givenIHaveEnteredTheName('Lisa Simpson');
        $this->component->givenIHaveEnteredTheEmail('lisa@simpson.com');

        $this->component->whenICreateANewUser();

        $this->component->thenIShouldBeRedirectedTo('login.html');
    }

    function testNotAdmin() {
        $this->session->givenIAmLoggedAsTheUser('Homer');
        $this->component->givenIHaveEnteredTheName('Lisa Simpson');
        $this->component->givenIHaveEnteredTheEmail('lisa@simpson.com');

        $this->component->whenICreateANewUser();

        $this->component->thenTheErrorMessageShouldBe('Access denied. Must be administrator.');
        $this->component->thenTheNewNameFieldShouldContain('Lisa Simpson');
        $this->component->thenTheEmailFieldShouldContain('lisa@simpson.com');
    }

    function testEmptyName() {
        $this->session->givenIAmLoggedInAsAdmin();
        $this->component->givenIHaveEnteredTheEmail('bart@simpson.com');

        $this->component->whenICreateANewUser();

        $this->component->thenTheErrorMessageShouldBe('Please provide name and email.');
        $this->component->thenTheEmailFieldShouldContain('bart@simpson.com');
    }

    function testEmptyEmail() {
        $this->session->givenIAmLoggedInAsAdmin();
        $this->component->givenIHaveEnteredTheName('Bart Simpson');

        $this->component->whenICreateANewUser();

        $this->component->thenTheErrorMessageShouldBe('Please provide name and email.');
        $this->component->thenTheNewNameFieldShouldContain('Bart Simpson');
    }

    function testAlreadyExistingEmail() {
        $this->session->givenIAmLoggedInAsAdmin();
        $this->user->givenTheUser_WithTheEmail('El Barto', 'bart@simpson.com');
        $this->component->givenIHaveEnteredTheName('Bart Simpson');
        $this->component->givenIHaveEnteredTheEmail('Bart@Simpson.com');

        $this->component->whenICreateANewUser();

        $this->component->thenTheErrorMessageShouldBe('Error while creating user. The email probably already exists.');
        $this->component->thenTheNewNameFieldShouldContain('Bart Simpson');
        $this->component->thenTheEmailFieldShouldContain('Bart@Simpson.com');
    }

    function testAlreadyExistingKey() {
        $this->session->givenIAmLoggedInAsAdmin();
        $this->user->givenTheUser_WithTheEmail_AndKey('Bart Simpson', 'bart@simpson.com', 'abc');
        $this->component->givenIHaveEnteredTheName('Lisa Simpson');
        $this->component->givenIHaveEnteredTheEmail('lisa@simpson.com');

        $this->key->givenTheNextGeneratedKeyIs('def');
        $this->key->givenTheNextGeneratedKeyIs('abc');

        $this->component->whenICreateANewUser();

        $this->user->thenThereShouldBe_Users(2);
        $this->user->thenThereShouldBeAUserWithTheName_TheEmailAndTheKey('Lisa Simpson', 'lisa@simpson.com', 'def');
    }

}