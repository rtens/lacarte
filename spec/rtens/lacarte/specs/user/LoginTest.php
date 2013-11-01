<?php
namespace spec\rtens\lacarte\specs\user;

use spec\rtens\lacarte\fixtures\model\UserFixture;
use spec\rtens\lacarte\fixtures\resource\user\LoginResourceFixture;
use spec\rtens\lacarte\fixtures\service\SessionFixture;
use spec\rtens\lacarte\Specification;

/**
 * @property UserFixture user <-
 * @property SessionFixture session <-
 * @property LoginResourceFixture component <-
 */
class LoginTest extends Specification {

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


}