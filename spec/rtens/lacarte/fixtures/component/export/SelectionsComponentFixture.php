<?php
namespace spec\rtens\lacarte\fixtures\component\export;

use rtens\lacarte\web\export\SelectionsComponent;
use rtens\lacarte\web\LaCarteModule;
use spec\rtens\lacarte\fixtures\component\ComponentFixture;
use spec\rtens\lacarte\fixtures\model\OrderFixture;
use spec\rtens\lacarte\fixtures\model\UserFixture;
use spec\rtens\lacarte\fixtures\service\SessionFixture;
use spec\rtens\lacarte\TestCase;
use watoki\factory\Factory;

/**
 * @property SelectionsComponent $component
 */
class SelectionsComponentFixture extends ComponentFixture {

    public static $CLASS = __CLASS__;

    /** @var OrderFixture */
    private $order;

    public function __construct(TestCase $test, Factory $factory, UserFixture $user, LaCarteModule $root,
                                SessionFixture $session, OrderFixture $order) {
        parent::__construct($test, $factory, $user, $root, $session);
        $this->order = $order;
    }

    public function whenIOpenTheSelectionsOf_WithToken($date, $token) {
        $this->model = $this->component->doGet($token, $date);
    }

    public function whenIOpenTheSelectionsWithToke($token) {
        $this->model = $this->component->doGet($token);
    }

    public function thenTheMenuShouldBeEmpty() {
        $this->test->assertCount(0, $this->getField('menu'));
    }

    public function thenThereShouldBe_Selections($int) {
        $this->test->assertCount($int, $this->getField('selections'));
    }

    public function thenTheErrorMessageShouldBe($string) {
        $this->test->assertEquals($string, $this->getField('error'));
    }

    public function thenTheDateShouldBe($string) {
        $this->test->assertEquals($string, $this->getField('menu/date'));
    }

    public function thenThereShouldBe_Dishes($int) {
        $this->test->assertCount($int, $this->getField('menu/dishes'));
    }

    public function thenDish_ShouldBe_InEnglish($int, $string) {
        $this->test->assertEquals($string, $this->getField("menu/dishes/$int/en"));
    }

    public function thenDish_ShouldBe_InGerman($int, $string) {
        $this->test->assertEquals($string, $this->getField("menu/dishes/$int/de"));
    }

    public function thenSelection_ShouldBeOfUser_ForDish($int, $userName, $dishText) {
        $base = "selections/$int";
        $this->test->assertEquals($this->user->getUser($userName)->id, $this->getField("$base/user/id"));
        $this->test->assertEquals($userName, $this->getField("$base/user/name"));
        $this->test->assertEquals($this->order->getDish($dishText)->id, $this->getField("$base/dish"));
    }

    public function thenTheAvatarOfTheUserOfSelection_ShouldBe($int, $string) {
        $this->test->assertEquals($string, $this->getField("selections/$int/user/avatar"));
    }

    protected function getComponentClass() {
        return SelectionsComponent::$CLASS;
    }
}