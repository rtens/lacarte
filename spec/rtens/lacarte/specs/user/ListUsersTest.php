<?php
namespace spec\rtens\lacarte\specs\user;

use spec\rtens\lacarte\fixtures\model\UserFixture;
use spec\rtens\lacarte\fixtures\resource\user\ListResourceFixture;
use spec\rtens\lacarte\fixtures\service\FileFixture;
use spec\rtens\lacarte\fixtures\service\SessionFixture;
use spec\rtens\lacarte\Specification;

/**
 * @property SessionFixture session <-
 * @property UserFixture user <-
 * @property FileFixture file <-
 * @property ListResourceFixture component <-
 */
class ListUsersTest extends Specification {

    function testZeroUsers() {
        $this->session->givenIAmLoggedInAsAdmin();
        $this->component->whenIAccessTheUserList();
        $this->component->thenTheUserListShouldBeEmpty();
    }

    function testThreeUsers() {
        $this->session->givenIAmLoggedInAsAdmin();
        $this->user->givenTheUser('Bart Simpson');
        $this->user->givenTheUser('Lisa Simpson');
        $this->user->givenTheUser('Homer Simpson');

        $this->component->whenIAccessTheUserList();

        $this->component->thenThereShouldBe_Users(3);
    }

    function testNotAdmin() {
        $this->component->whenIAccessTheUserList();
        $this->component->thenIShouldBeRedirectedTo('../order/list.html');
    }

    function testAvatars() {
        $this->session->givenIAmLoggedInAsAdmin();
        $this->user->givenTheUser('Bart Simpson');
        $this->file->given_HasAnAvatar('Bart Simpson');

        $this->component->whenIAccessTheUserList();

        $this->component->thenTheAvatarOfUserAtPosition_ShouldBe(1, 'http://lacarte/user/avatars/1.jpg');
    }

}