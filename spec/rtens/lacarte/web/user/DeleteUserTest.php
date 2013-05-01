<?php
namespace spec\rtens\lacarte\web\user;

use rtens\lacarte\web\user\ListComponent;
use spec\rtens\lacarte\Test;
use spec\rtens\lacarte\web\ComponentTest;
use spec\rtens\lacarte\web\ComponentTest_Then;
use spec\rtens\lacarte\web\ComponentTest_When;

/**
 * @property DeleteUserTest_When when
 * @property DeleteUserTest_Then then
 */
class DeleteUserTest extends ComponentTest {

    function testNotAdmin() {
        $this->given->theUser('X');
        $this->when->iDeleteUser('X');
        $this->then->iShouldBeRedirectedTo('../order/list.html');
    }

    function testDelete() {
        $this->given->iAmLoggedInAsAdmin();
        $this->given->theUser('X');

        $this->when->iDeleteUser('X');

        $this->then->theUser_ShouldBeDeleted('X');
    }

}

/**
 * @property DeleteUserTest test
 * @property ListComponent component
 */
class DeleteUserTest_When extends ComponentTest_When {

    function __construct(Test $test) {
        parent::__construct($test);
        $this->createDefaultComponent(ListComponent::$CLASS);
    }

    public function iDeleteUser($name) {
        $this->component->doDelete($this->test->given->users[$name]->id);
    }
}

/**
 * @property DeleteUserTest test
 */
class DeleteUserTest_Then extends ComponentTest_Then {

    public function theUser_ShouldBeDeleted($name) {
        $method = $this->test->given->userInteractor->__mock()->method('delete');
        $this->test->assertTrue($method->wasCalled());
        $this->test->assertEquals($this->test->given->users[$name]->id, $method->getCalledArgumentAt(0, 0)->id);
    }
}