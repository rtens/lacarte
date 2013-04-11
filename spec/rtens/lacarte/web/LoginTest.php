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
     * @var Map
     */
    public $session;

    function __construct(Test $test) {
        parent::__construct($test);

        $this->userInteractor = $this->test->mf->createMock(UserInteractor::$CLASS);
        $this->session = new Map();
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
        $component = new Login($this->test->factory, new Path(), null,
            $this->test->given->userInteractor,
            $this->test->given->session);

        $this->model = $component->doPost($this->test->given->email, $this->test->given->password);
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
}