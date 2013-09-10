<?php
namespace spec\rtens\lacarte\fixtures\component;

use rtens\lacarte\web\LaCarteModule;
use spec\rtens\lacarte\fixtures\Fixture;
use spec\rtens\lacarte\fixtures\service\SessionFixture;
use spec\rtens\lacarte\fixtures\model\UserFixture;
use spec\rtens\lacarte\TestCase;
use watoki\curir\Response;
use watoki\factory\Factory;

abstract class ComponentFixture extends Fixture {

    protected $model;

    protected $component;

    abstract protected function getComponentClass();

    public function __construct(TestCase $test, Factory $factory, UserFixture $user, LaCarteModule $root, SessionFixture $session) {
        parent::__construct($test, $factory);
        $this->user = $user;

        $this->component = $factory->getInstance($this->getComponentClass(), array(
            'parent' => $root
        ));
    }

    public function thenIShouldBeRedirectedTo($url) {
        $this->test->assertNull($this->model);
        $this->test->assertEquals($url,
            $this->component->getResponse()->getHeaders()->get(Response::HEADER_LOCATION));
    }

    protected function getFieldIn($string, $field) {
        $this->test->assertTrue(is_array($field), $string . ' is not an array');

        foreach (explode('/', $string) as $key) {
            if (!array_key_exists($key, $field)) {
                throw new \Exception("Could not find '$key' in " . json_encode($field));
            }
            $field = $field[$key];
        }
        return $field;
    }

    protected function getField($string) {
        return $this->getFieldIn($string, $this->model);
    }

}