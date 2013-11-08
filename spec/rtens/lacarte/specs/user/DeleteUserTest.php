<?php
namespace spec\rtens\lacarte\specs\user;

use spec\rtens\lacarte\fixtures\model\UserFixture;
use spec\rtens\lacarte\fixtures\resource\user\ListResourceFixture;
use spec\rtens\lacarte\fixtures\service\SessionFixture;
use spec\rtens\lacarte\Specification;

/**
 * @property UserFixture user <-
 * @property SessionFixture session <-
 * @property ListResourceFixture component <-
 */
class DeleteUserTest extends Specification {

    function testDeleteAUser() {
        $this->user->givenTheUser('Bart Simpson');
        $this->user->givenTheUser('Lisa Simpson');
        $this->session->givenIAmLoggedInAsAdmin();

        $this->component->whenIDeleteTheUser('Bart Simpson');

        $this->user->thenThereShouldBe_Users(1);
        $this->user->thenThereShouldBeAUserWithTheTheName('Lisa Simpson');
    }

    function testNotAdmin() {
        $this->user->givenTheUser('Bart Simpson');

        $this->component->whenIDeleteTheUser('Bart Simpson');

        $this->component->thenIShouldBeRedirectedTo('../order/list.html');
    }

}