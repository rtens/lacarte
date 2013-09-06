<?php
namespace spec\rtens\lacarte\fixture\model;

use rtens\lacarte\core\Session;
use rtens\lacarte\model\Group;
use rtens\mockster\Mock;
use rtens\mockster\MockFactory;
use rtens\mockster\Mockster;
use spec\rtens\lacarte\fixture\Fixture;
use spec\rtens\lacarte\TestCase;
use watoki\factory\Factory;

class SessionFixture extends Fixture {

    public static $CLASS = __CLASS__;

    /** @var UserFixture */
    public $user;

    /** @var Mock */
    private $session;

    public function __construct(TestCase $test, Factory $factory, UserFixture $user) {
        parent::__construct($test, $factory);

        $this->user = $user;

        $this->session = $this->mockFactory->createMock(Session::$CLASS);
        $factory->setSingleton(Session::$CLASS, $this->session);
    }

    public function givenIAmLoggedInAsAdmin() {
        $this->session->__mock()->method('get')->willReturn($this->user->getGroup()->id)->withArguments('admin');
        $this->session->__mock()->method('has')->willReturn(true)->withArguments('admin');
    }

    public function givenIAmLoggedAsTheUser($name) {
        $this->user->givenTheUser($name);
        $user = $this->user->getUser($name);

        $this->session->__mock()->method('get')->willReturn($user->getKey())->withArguments('key');
        $this->session->__mock()->method('has')->willReturn(true)->withArguments('key');
    }
}