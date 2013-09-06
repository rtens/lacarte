<?php
namespace spec\rtens\lacarte\user;

use spec\rtens\lacarte\fixture\component\UserComponentFixture;
use spec\rtens\lacarte\fixture\model\SessionFixture;
use spec\rtens\lacarte\fixture\model\UserFixture;
use spec\rtens\lacarte\TestCase;

class CreateUserTest extends TestCase {

    /** @var UserFixture */
    public $user;

    /** @var SessionFixture */
    public $session;

    /** @var UserComponentFixture */
    public $component;

    function testCreateUserSuccessfully() {
        $this->session->givenIAmLoggedInAsAdmin();
        $this->component->givenIHaveEnteredTheName('Bart Simpson');
        $this->component->givenIHaveEnteredTheEmail('bart@simpson.com');

        $this->component->whenICreateANewUser();

        $this->component->thenTheSuccessMessageShouldBe('The user Bart Simpson was created.');
        $this->user->thenThereShouldBe_Users(1);
        $this->user->thenThereShouldBeAUserWithTheName_TheEmailAndAKey('Bart Simpson', 'bart@simpson.com');
    }

    protected function setUp() {
        parent::setUp();

        $this->session = $this->useFixture(SessionFixture::$CLASS);
        $this->component = $this->useFixture(UserComponentFixture::$CLASS);
        $this->user = $this->useFixture(UserFixture::$CLASS);
    }

}