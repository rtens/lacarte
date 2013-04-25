<?php
namespace spec\rtens\lacarte\web\user;

use rtens\lacarte\UserInteractor;
use rtens\lacarte\web\user\ListComponent;
use spec\rtens\lacarte\Test;
use spec\rtens\lacarte\web\ComponentTest;
use spec\rtens\lacarte\web\ComponentTest_Given;
use spec\rtens\lacarte\web\ComponentTest_When;
use watoki\curir\Path;

/**
 * @property ListUsersTest_Given given
 * @property ListUsersTest_When when
 */
class ListUsersTest extends ComponentTest {

    function testZeroUsers() {
        $this->given->iAmLoggedInAsAdmin();
        $this->when->iAccessThePage();
        $this->then->_shouldBe('user', array());
    }

    function testNonZeroUsers() {
        $this->given->iAmLoggedInAsAdmin();
        $this->given->theUser('UserA');
        $this->given->theUser('UserC');
        $this->given->theUser('UserB');

        $this->when->iAccessThePage();

        $this->then->_shouldHaveTheSize('user', 3);
    }

    function testNotAdmin() {
        $this->when->iAccessThePage();
        $this->then->iShouldBeRedirectedTo('../order/list.html');
    }

}

/**
 * @property ListUsersTest test
 */
class ListUsersTest_Given extends ComponentTest_Given {

    /** @var UserInteractor */
    public $userInteractor;

    function __construct(Test $test) {
        parent::__construct($test);
        $this->userInteractor = $test->factory->getInstance(UserInteractor::$CLASS);
    }

    public function theUser($string) {
        $this->userInteractor->createUser(1, $string, $string . '@example.com');
    }
}

/**
 * @property ListUsersTest test
 * @property ListComponent component
 */
class ListUsersTest_When extends ComponentTest_When {

    function __construct(Test $test) {
        parent::__construct($test);
        $this->createDefaultComponent(ListComponent::$CLASS, array(
            'userInteractor' => $this->test->given->userInteractor
        ));
    }

    public function iAccessThePage() {
        $this->model = $this->component->doGet();
    }
}