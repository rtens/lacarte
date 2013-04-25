<?php
namespace spec\rtens\lacarte\web\user;

use rtens\lacarte\UserInteractor;
use rtens\lacarte\core\Session;
use rtens\lacarte\model\Group;
use rtens\lacarte\model\User;
use rtens\lacarte\web\user\LoginComponent;
use rtens\mockster\Mock;
use spec\rtens\lacarte\Test;
use spec\rtens\lacarte\Test_Given;
use spec\rtens\lacarte\Test_Then;
use spec\rtens\lacarte\Test_When;
use spec\rtens\lacarte\web\ComponentTest;
use spec\rtens\lacarte\web\ComponentTest_Given;
use spec\rtens\lacarte\web\ComponentTest_Then;
use spec\rtens\lacarte\web\ComponentTest_When;
use watoki\collections\Map;
use watoki\curir\Path;
use watoki\curir\Response;

/**
 * @property LoginTest_Given given
 * @property LoginTest_When when
 * @property LoginTest_Then then
 */
class LoginTest extends ComponentTest {

    function testSuccess() {
        $this->given->iEnteredTheCorrectCredentialsForGroup('Group One');

        $this->when->iLoginAsAdmin();

        $this->then->theSessionShouldContain_WithValue('admin', 1);
        $this->then->iShouldBeRedirectedTo('../order/list.html');
    }

    function testFail() {
        $this->given->iHaveEnteredTheWrongCredentials('my@email.com', 'bla');

        $this->when->iLoginAsAdmin();

        $this->then->_shouldNotBeEmpty('error');
        $this->then->_shouldBe('email', 'my@email.com');
    }

    function testAccessPage() {
        $this->when->iGoToTheLoginPage();
        $this->then->theModelShouldBe('{"error":null}');
    }

    function testAlreadyLoggedIn() {
        $this->given->iAmAlreadyLoggedInForGroup('test');

        $this->when->iGoToTheLoginPage();

        $this->then->iShouldBeRedirectedTo('../order/list.html');
    }

    function testLogOut() {
        $this->given->iAmAlreadyLoggedInForGroup('test');

        $this->when->iLogOut();

        $this->then->iShouldBeRedirectedTo('login.html');
        $this->then->theSessionShouldNotContain('admin');
        $this->then->theSessionShouldNotContain('key');
    }

    function testLogInWithKey() {
        $this->given->theUser_WithKey_InGroup('Mark', 'myKey', 44);
        $this->given->iHaveEnteredTheCorrectKey();

        $this->when->iLoginAsUser();

        $this->then->theSessionShouldContain_WithValue('key', 'myKey');
        $this->then->theSessionShouldNotContain('admin');
    }

    function testWrongKey() {
        $this->given->iHaveEnteredAnIncorrectKey();
        $this->when->iLoginAsUser();
        $this->then->_shouldNotBeEmpty('error');
    }
}

/**
 * @property LoginTest test
 */
class LoginTest_Given extends ComponentTest_Given {

    /**
     * @var UserInteractor|Mock
     */
    public $userInteractor;

    public $email;

    public $password;

    /**
     * @var Session|Mock
     */
    public $session;

    /** @var User */
    public $user;

    function __construct(Test $test) {
        parent::__construct($test);
        $this->userInteractor = $this->test->mf->createMock(UserInteractor::$CLASS);
    }

    public function iEnteredTheCorrectCredentialsForGroup($groupName) {
        $group = new Group($groupName, '', '');
        $group->id = 1;
        $this->userInteractor->__mock()->method('authorizeAdmin')->willReturn($group);
    }

    public function iHaveEnteredTheWrongCredentials($email, $password) {
        $this->userInteractor->__mock()->method('authorizeAdmin')->willReturn(null);
        $this->email = $email;
        $this->password = $password;
    }

    public function iAmAlreadyLoggedInForGroup($groupName) {
        $group = new Group($groupName, '', '');
        $group->id = 1;
        $this->session->set('admin', $group->id);
    }

    public function theUser_WithKey_InGroup($name, $key, $groupId) {
        $this->user = new User($groupId, $name, "$name@example.com",  $key);
    }

    public function iHaveEnteredTheCorrectKey() {
        $this->userInteractor->__mock()->method('authorizeUser')->willReturn($this->user);
    }

    public function iHaveEnteredAnIncorrectKey() {
        $this->userInteractor->__mock()->method('authorizeUser')->willReturn(null);
    }
}

/**
 * @property LoginTest test
 * @property LoginComponent component
 */
class LoginTest_When extends ComponentTest_When {

    function __construct(Test $test) {
        parent::__construct($test);
        $this->createDefaultComponent(LoginComponent::$CLASS, array(
            'userInteractor' => $this->test->given->userInteractor
        ));
    }

    public function iLoginAsAdmin() {
        $this->model = $this->component->doLoginAdmin(
            $this->test->given->email, $this->test->given->password);
    }

    public function iGoToTheLoginPage() {
        $this->model = $this->component->doGet();
    }

    public function iLogOut() {
        $this->model = $this->component->doLogout();
    }

    public function iLoginAsUser() {
        $this->model = $this->component->doPost('whatever');
    }
}

/**
 * @property LoginTest test
 */
class LoginTest_Then extends ComponentTest_Then {

    public function theSessionShouldContain_WithValue($field, $value) {
        $this->test->assertEquals($value, $this->test->given->session->get($field));
    }

    public function theSessionShouldNotContain($key) {
        $this->test->assertFalse($this->test->given->session->has($key));
    }
}