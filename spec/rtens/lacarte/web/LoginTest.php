<?php
namespace spec\rtens\lacarte\web;

use rtens\lacarte\UserInteractor;
use rtens\lacarte\core\Session;
use rtens\lacarte\model\Group;
use rtens\lacarte\web\user\Login;
use rtens\mockster\Mock;
use spec\rtens\lacarte\Test;
use spec\rtens\lacarte\Test_Given;
use spec\rtens\lacarte\Test_Then;
use spec\rtens\lacarte\Test_When;
use watoki\collections\Map;
use watoki\curir\Path;
use watoki\curir\Response;

/**
 * @property LoginTest_Given given
 * @property LoginTest_When when
 * @property LoginTest_Then then
 */
class LoginTest extends Test {

    function testSuccess() {
        $this->given->iEnteredTheCorrectCredentialsForGroup('Group One');

        $this->when->iLoginAsAdmin();

        $this->then->theSessionShouldContain_WithValue('group', 1);
        $this->then->theSessionShouldContain_WithValue('isAdmin', true);
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
        $this->then->theModelShouldBe("[]");
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
        $this->then->theSessionShouldNotContain('group');
        $this->then->theSessionShouldNotContain('isAdmin');
    }
}

/**
 * @property LoginTest test
 */
class LoginTest_Given extends Test_Given {

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

    function __construct(Test $test) {
        parent::__construct($test);

        $this->userInteractor = $this->test->mf->createMock(UserInteractor::$CLASS);
        $this->session = $this->test->mf->createMock(Session::$CLASS);
        $sessionVars = new Map();
        $this->session->__mock()->method('set')->willCall(function ($key, $value) use ($sessionVars) {
            $sessionVars->set($key, $value);
        });
        $this->session->__mock()->method('get')->willCall(function ($key) use ($sessionVars) {
            return $sessionVars->get($key);
        });
        $this->session->__mock()->method('has')->willCall(function ($key) use ($sessionVars) {
            return $sessionVars->has($key);
        });
        $this->session->__mock()->method('remove')->willCall(function ($key) use ($sessionVars) {
            return $sessionVars->remove($key);
        });
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
        $this->session->set('group', $group->id);
        $this->session->set('isAdmin', true);
    }
}

/**
 * @property LoginTest test
 */
class LoginTest_When extends Test_When {

    public $model;

    /**
     * @var Response
     */
    public $response;

    public function iLoginAsAdmin() {
        $component = $this->createComponent();
        $this->model = $component->doLoginAdmin($this->test->given->email, $this->test->given->password);
        $this->response = $component->getResponse();
    }

    public function iGoToTheLoginPage() {
        $component = $this->createComponent();
        $this->model = $component->doGet();
        $this->response = $component->getResponse();
    }

    /**
     * @return Login
     */
    private function createComponent() {
        return new Login($this->test->factory, new Path(), null,
            $this->test->given->userInteractor,
            $this->test->given->session);
    }

    public function iLogOut() {
        $component = $this->createComponent();
        $this->model = $component->doLogout();
        $this->response = $component->getResponse();
    }
}

/**
 * @property LoginTest test
 */
class LoginTest_Then extends Test_Then {

    public function iShouldBeRedirectedTo($url) {
        $this->test->assertNull($this->test->when->model);
        $this->test->assertEquals($url, $this->test->when->response->getHeaders()->get(Response::HEADER_LOCATION));
    }

    public function _shouldBe($field, $value) {
        $this->test->assertEquals($value, $this->getField($field));
    }

    public function _shouldNotBeEmpty($field) {
        $this->test->assertNotEmpty($this->getField($field));
    }

    private function getField($field) {
        return $this->getFieldIn($field, $this->test->when->model);
    }

    public function theSessionShouldContain_WithValue($field, $value) {
        $this->test->assertEquals($value, $this->test->given->session->get($field));
    }

    public function theModelShouldBe($json) {
        $this->test->assertEquals($json, json_encode($this->test->when->model));
    }

    public function theSessionShouldNotContain($key) {
        $this->test->assertFalse($this->test->given->session->has($key));
    }
}