<?php
namespace spec\rtens\lacarte\fixtures\component\export;

use rtens\lacarte\web\export\SelectionsComponent;
use spec\rtens\lacarte\fixtures\component\ComponentFixture;
use spec\rtens\lacarte\fixtures\model\OrderFixture;
use spec\rtens\lacarte\fixtures\model\UserFixture;

/**
 * @property SelectionsComponent component
 * @property UserFixture user <-
 * @property OrderFixture order <-
 */
class SelectionsComponentFixture extends ComponentFixture {

    public static $CLASS = __CLASS__;

    public function whenIOpenTheSelectionsOf_WithToken($date, $token) {
        $this->model = $this->component->doGet($token, $date);
    }

    public function whenIOpenTheSelectionsWithToke($token) {
        $this->model = $this->component->doGet($token);
    }

    public function thenTheMenuShouldBeEmpty() {
        $this->spec->assertCount(0, $this->getField('menu'));
    }

    public function thenThereShouldBe_Selections($int) {
        $this->spec->assertCount($int, $this->getField('selections'));
    }

    public function thenTheErrorMessageShouldBe($string) {
        $this->spec->assertEquals($string, $this->getField('error'));
    }

    public function thenTheDateShouldBe($string) {
        $this->spec->assertEquals($string, $this->getField('menu/date'));
    }

    public function thenThereShouldBe_Dishes($int) {
        $this->spec->assertCount($int, $this->getField('menu/dishes'));
    }

    public function thenDish_ShouldBe_InEnglish($int, $string) {
        $this->spec->assertEquals($string, $this->getField("menu/dishes/$int/en"));
    }

    public function thenDish_ShouldBe_InGerman($int, $string) {
        $this->spec->assertEquals($string, $this->getField("menu/dishes/$int/de"));
    }

    public function thenSelection_ShouldBeOfUser_ForDish($int, $userName, $dishText) {
        $base = "selections/$int";
        $this->spec->assertEquals($this->user->getUser($userName)->id, $this->getField("$base/user/id"));
        $this->spec->assertEquals($userName, $this->getField("$base/user/name"));
        $this->spec->assertEquals($this->order->getDish($dishText)->id, $this->getField("$base/dish"));
    }

    public function thenTheAvatarOfTheUserOfSelection_ShouldBe($int, $string) {
        $this->spec->assertEquals($string, $this->getField("selections/$int/user/avatar"));
    }

    protected function getComponentClass() {
        return SelectionsComponent::$CLASS;
    }
}