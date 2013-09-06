<?php
namespace spec\rtens\lacarte\fixture\model;

use rtens\lacarte\core\Session;
use rtens\lacarte\model\Group;
use rtens\mockster\Mockster;
use spec\rtens\lacarte\fixture\Fixture;

class SessionFixture extends Fixture {

    public static $CLASS = __CLASS__;

    /** @var Group */
    public $group;

    /** @var Session */
    private $session;

    public function setUp() {
        parent::setUp();

        $this->session = $this->mockFactory->createMock(Session::$CLASS);
        $this->session->__mock()->mockMethods(Mockster::F_NONE);
        $this->factory->setSingleton(Session::$CLASS, $this->session);

        $this->group = new Group('Test', '', '');
        $this->group->id = 1;
    }

    public function givenIAmLoggedInAsAdmin() {
        $this->session->set('admin', $this->group->id);
    }
}