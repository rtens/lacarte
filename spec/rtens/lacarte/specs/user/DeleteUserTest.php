<?php
namespace spec\rtens\lacarte\specs\user;

use rtens\mockster\MockFactory;
use spec\rtens\lacarte\fixtures\component\user\ListComponentFixture;
use spec\rtens\lacarte\fixtures\service\SessionFixture;
use spec\rtens\lacarte\fixtures\model\UserFixture;
use spec\rtens\lacarte\TestCase;

class DeleteUserTest extends TestCase {

    /** @var UserFixture */
    public $user;

    /** @var ListComponentFixture */
    public $component;

    /** @var \spec\rtens\lacarte\fixtures\service\SessionFixture */
    public $session;

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

    protected function setUp() {
        parent::setUp();

        $this->session = $this->useFixture(SessionFixture::$CLASS);
        $this->user = $this->useFixture(UserFixture::$CLASS);
        $this->component = $this->useFixture(ListComponentFixture::$CLASS);
    }


}