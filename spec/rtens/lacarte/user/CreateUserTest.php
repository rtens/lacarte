<?php
namespace spec\rtens\lacarte\user;

use spec\rtens\lacarte\fixture\component\user\ListComponentFixture;
use spec\rtens\lacarte\fixture\model\KeyGeneratorFixture;
use spec\rtens\lacarte\fixture\model\SessionFixture;
use spec\rtens\lacarte\fixture\model\UserFixture;
use spec\rtens\lacarte\TestCase;

class CreateUserTest extends TestCase {

    /** @var UserFixture */
    public $user;

    /** @var SessionFixture */
    public $session;

    /** @var ListComponentFixture */
    public $component;

    /** @var KeyGeneratorFixture */
    public $key;

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

    protected function setUp() {
        parent::setUp();

        $this->key = $this->useFixture(KeyGeneratorFixture::$CLASS);
        $this->session = $this->useFixture(SessionFixture::$CLASS);
        $this->component = $this->useFixture(ListComponentFixture::$CLASS);
        $this->user = $this->useFixture(UserFixture::$CLASS);
    }

}