<?php
namespace spec\rtens\lacarte\web;

use rtens\lacarte\core\Session;
use rtens\lacarte\model\Group;
use rtens\mockster\Mock;
use rtens\mockster\Mockster;
use spec\rtens\lacarte\Test;
use spec\rtens\lacarte\Test_Given;
use spec\rtens\lacarte\Test_Then;
use spec\rtens\lacarte\Test_When;
use watoki\collections\Map;
use watoki\curir\Response;
use watoki\curir\controller\Component;

/**
 * @property ComponentTest_Given given
 * @property ComponentTest_When when
 * @property ComponentTest_Then then
 */
abstract class ComponentTest extends Test {

}

/**
 * @property ComponentTest test
 */
class ComponentTest_Given extends Test_Given {

    /**
     * @var Session|Mock
     */
    public $session;

    /**
     * @var Group
     */
    public $group;

    function __construct(Test $test) {
        parent::__construct($test);

        $this->group = new Group('test', '', '');
        $this->group->id = 42;

        $this->session = $this->test->mf->createMock(Session::$CLASS);
        $this->session->__mock()->mockMethods(Mockster::F_NONE);
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

    public function iAmLoggedIn() {
        $this->session->set('group', $this->group->id);
    }

    public function iAmAdmin() {
        $this->iAmLoggedIn();
        $this->session->set('isAdmin', true);
    }

}

/**
 * @property ComponentTest test
 */
class ComponentTest_When extends Test_When {

    public $model;

    /**
     * @var Response
     */
    public $response;

    /** @var Component */
    public $component;

}

/**
 * @property ComponentTest test
 */
class ComponentTest_Then extends Test_Then {

    public function iShouldBeRedirectedTo($url) {
        $this->test->assertNull($this->test->when->model);
        $this->test->assertEquals($url,
            $this->test->when->component->getResponse()->getHeaders()->get(Response::HEADER_LOCATION));
    }

    public function _shouldBe($field, $value) {
        $this->test->assertEquals($value, $this->getField($field));
    }

    public function _shouldNotBeEmpty($field) {
        $this->test->assertNotEmpty($this->getField($field));
    }

    protected function getField($field) {
        return $this->getFieldIn($field, $this->test->when->model);
    }

    public function theModelShouldBe($json) {
        $this->test->assertEquals($json, json_encode($this->test->when->model));
    }

    public function _shouldHaveTheSize($field, $int) {
        $this->test->assertEquals($int, count($this->getField($field)));
    }

}