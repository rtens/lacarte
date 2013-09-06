<?php
namespace spec\rtens\lacarte\fixture\component\user;

use rtens\lacarte\web\LaCarteModule;
use rtens\lacarte\web\user\ListComponent;
use rtens\mockster\MockFactory;
use spec\rtens\lacarte\fixture\Fixture;
use spec\rtens\lacarte\fixture\model\UserFixture;
use spec\rtens\lacarte\TestCase;
use watoki\curir\Response;
use watoki\factory\Factory;

class ListComponentFixture extends Fixture {

    public static $CLASS = __CLASS__;

    private $newName;

    private $newEmail;

    /** @var ListComponent */
    private $component;

    private $model;

    public function __construct(TestCase $test, Factory $factory, UserFixture $user, LaCarteModule $root) {
        parent::__construct($test, $factory);
        $this->user = $user;

        $this->component = $factory->getInstance(ListComponent::$CLASS, array(
            'parent' => $root
        ));
    }

    public function givenIHaveEnteredTheName($string) {
        $this->newName = $string;
    }

    public function givenIHaveEnteredTheEmail($string) {
        $this->newEmail = $string;
    }

    public function whenICreateANewUser() {
        $this->model = $this->component->doPost($this->newName, $this->newEmail);
    }

    public function whenIAccessTheUserList() {
        $this->model = $this->component->doGet();
    }

    public function whenIDeleteTheUser($name) {
        $this->model = $this->component->doDelete($this->user->getUser($name)->id);
    }

    public function thenTheSuccessMessageShouldBe($string) {
        $this->test->assertEquals($string, $this->getFieldIn('success', $this->model));
    }

    public function thenTheErrorMessageShouldBe($string) {
        $this->test->assertEquals($string, $this->getFieldIn('error', $this->model));
    }

    public function thenIShouldBeRedirectedTo($url) {
        $this->test->assertNull($this->model);
        $this->test->assertEquals($url,
            $this->component->getResponse()->getHeaders()->get(Response::HEADER_LOCATION));
    }

    public function thenTheNewNameFieldShouldContain($string) {
        $this->test->assertEquals($string, $this->getFieldIn('name/value', $this->model));
    }

    public function thenTheEmailFieldShouldContain($string) {
        $this->test->assertEquals($string, $this->getFieldIn('email/value', $this->model));
    }

    protected function getFieldIn($string, $field) {
        $this->test->assertNotNull($field, $string . ' is null');

        foreach (explode('/', $string) as $key) {
            if (!array_key_exists($key, $field)) {
                throw new \Exception("Could not find '$key' in " . json_encode($field));
            }
            $field = $field[$key];
        }
        return $field;
    }

    public function thenTheUserListShouldBeEmpty() {
        $this->test->assertCount(0, $this->getFieldIn('user', $this->model));
    }

    public function thenThereShouldBe_Users($count) {
        $this->test->assertCount($count, $this->getFieldIn('user', $this->model));
    }

    public function thenTheAvatarOfUserAtPosition_ShouldBe($position, $imgSrc) {
        $i = $position - 1;
        $this->test->assertEquals($imgSrc, $this->getFieldIn("user/$i/avatar/src", $this->model));
    }
}