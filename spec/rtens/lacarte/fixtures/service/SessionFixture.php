<?php
namespace spec\rtens\lacarte\fixtures\service;

use rtens\lacarte\core\Session;
use rtens\mockster\Mock;
use rtens\mockster\MockFactory;
use rtens\mockster\Mockster;
use spec\rtens\lacarte\fixtures\model\UserFixture;
use spec\rtens\lacarte\Specification;
use watoki\factory\Factory;
use watoki\scrut\Fixture;

/**
 * @property UserFixture user <-
 */
class SessionFixture extends Fixture {

    public static $CLASS = __CLASS__;

    /** @var Mock */
    private $session;

    public function __construct(Specification $spec, Factory $factory) {
        parent::__construct($spec, $factory);

        $this->session = $spec->mockFactory->createMock(Session::$CLASS);
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

    public function thenIShouldBeLoggedInAsAdmin() {
        $this->spec->assertTrue($this->session->__mock()->method('set')->wasCalledWith(array('key' => 'admin')));
    }

    public function thenIShouldNotBeLoggedInAsUser() {
        $this->spec->assertFalse($this->session->__mock()->method('set')->wasCalledWith(array('key' => 'key')));
    }

    public function thenIShouldNotBeLoggedInAsAdmin() {
        $this->spec->assertFalse($this->session->__mock()->method('set')->wasCalledWith(array('key' => 'admin')));
    }

    public function thenIShouldBeLoggedInAs($userName) {
        $this->spec->assertTrue($this->session->__mock()->method('set')->wasCalledWith(array(
            'key' => 'key',
            'value' => $this->user->getUser($userName)->getKey()
        )));
    }

    public function thenIShouldBeLoggedOut() {
        $remove = $this->session->__mock()->method('remove');
        $this->spec->assertTrue($remove->wasCalledWith(array('key' => 'admin')));
        $this->spec->assertTrue($remove->wasCalledWith(array('key' => 'key')));
    }
}