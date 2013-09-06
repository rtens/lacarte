<?php
namespace spec\rtens\lacarte\user;

use rtens\mockster\MockFactory;
use spec\rtens\lacarte\fixture\component\user\LoginComponentFixture;
use spec\rtens\lacarte\fixture\model\SessionFixture;
use spec\rtens\lacarte\fixture\model\UserFixture;
use spec\rtens\lacarte\TestCase;

class LoginTest extends TestCase {

    /** @var UserFixture */
    public $user;

    /** @var LoginComponentFixture */
    public $component;

    /** @var SessionFixture */
    public $session;

    public function background() {
        $this->user->givenTheGroup_WithTheAdminEmail_AndPassword('Group', 'admin@example.com', 'password');
    }

    public function testOpenPage() {
        $this->component->whenIOpenThePage();
        $this->component->thenThereShouldBeNoErrorMessage();
    }

    public function testLoginAsAdminSuccessfully() {
        $this->component->givenIHaveEnteredTheAdminEmail('admin@example.com');
        $this->component->givenIHaveEnteredTheAdminPassword('password');

        $this->component->whenILogInAsAdmin();

        $this->session->thenIShouldBeLoggedInAsAdmin();
        $this->component->thenIShouldBeRedirectedTo('../order/list.html');
    }

    function testWrongAdminEmail() {
        $this->component->givenIHaveEnteredTheAdminEmail('wrong@example.com');
        $this->component->givenIHaveEnteredTheAdminPassword('password');

        $this->component->whenILogInAsAdmin();

        $this->component->thenTheErrorMessageShouldBe('Could not find group for given email and password');
        $this->component->thenTheAdminEmailFieldShouldContain('wrong@example.com');
    }

    function testWrongAdminPassword() {
        $this->component->givenIHaveEnteredTheAdminEmail('admin@example.com');
        $this->component->givenIHaveEnteredTheAdminPassword('wrong');

        $this->component->whenILogInAsAdmin();

        $this->component->thenTheErrorMessageShouldBe('Could not find group for given email and password');
        $this->component->thenTheAdminEmailFieldShouldContain('admin@example.com');
    }

    function testAlreadyLoggedIn() {
        $this->session->givenIAmLoggedInAsAdmin();

        $this->component->whenIOpenThePage();

        $this->component->thenIShouldBeRedirectedTo('../order/list.html');
    }

    function testLogOut() {
        $this->session->givenIAmLoggedInAsAdmin();

        $this->component->whenILogOut();

        $this->component->thenIShouldBeRedirectedTo('login.html');
        $this->session->thenIShouldBeLoggedOut();
    }

    function testLogInUser() {
        $this->user->givenTheUser_WithTheEmail_AndKey('Bart Simpson', 'bart@simpson.com', 'myKey');
        $this->component->givenIHaveEnterTheKey('myKey');

        $this->component->whenILogInAsUser();

        $this->session->thenIShouldBeLoggedInAs('Bart Simpson');
        $this->session->thenIShouldNotBeLoggedInAsAdmin();
    }

    function testWrongKey() {
        $this->user->givenTheUser_WithTheEmail_AndKey('Bart Simpson', 'bart@simpson.com', 'myKey');
        $this->component->givenIHaveEnterTheKey('notMyKey');

        $this->component->whenILogInAsUser();

        $this->component->thenTheErrorMessageShouldBe('You entered an invalid key');
    }

    protected function setUp() {
        parent::setUp();

        $this->session = $this->useFixture(SessionFixture::$CLASS);
        $this->user = $this->useFixture(UserFixture::$CLASS);
        $this->component = $this->useFixture(LoginComponentFixture::$CLASS);

        $this->background();
    }


}