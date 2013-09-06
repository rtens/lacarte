<?php
namespace spec\rtens\lacarte\user;

use rtens\mockster\MockFactory;
use spec\rtens\lacarte\fixture\component\user\ListComponentFixture;
use spec\rtens\lacarte\fixture\model\FileFixture;
use spec\rtens\lacarte\fixture\model\SessionFixture;
use spec\rtens\lacarte\fixture\model\UserFixture;
use spec\rtens\lacarte\TestCase;

class ListUsersTest extends TestCase {

    /** @var SessionFixture */
    public $session;

    /** @var UserFixture */
    public $user;

    /** @var ListComponentFixture */
    public $component;

    /** @var FileFixture */
    public $file;

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

    protected function setUp() {
        parent::setUp();

        $this->session = $this->useFixture(SessionFixture::$CLASS);
        $this->user = $this->useFixture(UserFixture::$CLASS);
        $this->component = $this->useFixture(ListComponentFixture::$CLASS);
        $this->file = $this->useFixture(FileFixture::$CLASS);
    }


}