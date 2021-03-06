<?php
namespace spec\rtens\lacarte\fixtures\resource\order;

use rtens\lacarte\model\Order;
use rtens\lacarte\web\order\SelectionsResource;
use spec\rtens\lacarte\fixtures\resource\ResourceFixture;
use spec\rtens\lacarte\fixtures\model\OrderFixture;

/**
 * @property SelectionsResource component
 * @property OrderFixture order <-
 */
class SelectionsResourceFixture extends ResourceFixture {

    public static $CLASS = __CLASS__;

    private $onlyWithoutSelection = false;

    /** @var null|Order */
    private $currentOrder;

    private $subject;

    private $body;

    public function whenIOpenThePageForOrder($name) {
        $this->responder = $this->component->doGet($this->order->getOrder($name)->id);
    }

    public function thenThereShouldBeNoSuccessMessage() {
        $this->thenTheSuccessMessageShouldBe(null);
    }

    public function thenThereShouldBeNoErrorMessage() {
        $this->thenTheErrorMessageShouldBe(null);
    }

    public function thenTheEditActionShouldGoTo($string) {
        $this->spec->assertEquals($string, $this->getField('actions/edit/href'));
    }

    public function thenTheExportByDishActionShouldGoTo($string) {
        $this->spec->assertEquals($string, $this->getField('actions/exportByDish/href'));
    }

    public function thenThereShouldBeNoExportByUserAction() {
        $this->spec->assertNull($this->getField('actions/exportByUser'));
    }

    public function thenNameOfTheOrderShouldBe($string) {
        $this->spec->assertEquals($string, $this->getField('order/name'));
        $this->spec->assertEquals($this->order->getOrder($string)->id, $this->getField('order/id/value'));
    }

    public function thenTheOrderShouldHave_Dates($int) {
        $this->spec->assertCount($int, $this->getField('order/date'));
    }

    public function thenDate_ShouldBe($int, $string) {
        $int--;
        $this->spec->assertEquals($string, $this->getField("order/date/$int"));
    }

    public function thenThereShouldBe_Users($int) {
        $this->spec->assertCount($int, $this->getField('order/user'));
    }

    public function thenTheNameOfUser_ShouldBe($int, $string) {
        $int--;
        $this->spec->assertEquals($string, $this->getField("order/user/$int/name"));
    }

    public function thenTheSelectionLinkOfUser_ShouldBe($int, $string) {
        $int--;
        $this->spec->assertEquals($string, $this->getField("order/user/$int/selectLink/href"));
    }

    public function thenUser_ShouldHave_Selections($int, $int1) {
        $int--;
        $this->spec->assertCount($int1, $this->getField("order/user/$int/selection"));
    }

    public function thenUser_ShouldHaveNothingSelectedForSelection($userNum, $selectionNum) {
        $userNum--;
        $selectionNum--;
        $this->spec->assertFalse($this->getField("order/user/$userNum/selection/$selectionNum/selected"));
    }

    public function thenTheSelectionOfUser_ShouldBe_WithTheTitle($userNum, $text, $title) {
        $userNum--;
        $this->spec->assertEquals($title, $this->getField("order/user/$userNum/selection/0/selected/title"));
        $this->spec->assertEquals($text, $this->getField("order/user/$userNum/selection/0/selected/_"));
    }

    public function givenIHaveEnteredTheSubject($string) {
        $this->subject = $string;
    }

    public function givenIHaveEnteredTheBody($string) {
        $this->body = $string;
    }

    public function whenISendTheMail() {
        $this->responder = $this->component->doSendMail($this->currentOrder->id, $this->subject,
            $this->body, $this->onlyWithoutSelection);
    }

    public function givenIOpenThePageForOrder($string) {
        $this->currentOrder = $this->order->getOrder($string);
    }

    public function givenIHaveSelectedToSendTheEmailOnlyToUsersWithoutSelection() {
        $this->onlyWithoutSelection = true;
    }

    public function thenTheSubjectFieldShouldContain($string) {
        $this->spec->assertEquals($string, $this->getField('email/subject/value'));
    }

    public function thenTheCheckboxToSendOnlyToUsersWithoutSelectionShouldBeChecked() {
        $this->spec->assertEquals('checked', $this->getField('email/onlyWithout/checked'));
    }

    public function thenTheBodyFieldShouldContain($string) {
        $this->spec->assertEquals($string, $this->getField('email/body'));
    }

    public function thenTheCheckboxToSendOnlyToUsersWithoutSelectionShouldNotBeChecked() {
        $this->spec->assertEquals(false, $this->getField('email/onlyWithout/checked'));
    }

    protected function getComponentClass() {
        return SelectionsResource::$CLASS;
    }

    public function thenTheSuccessMessageShouldBe($value) {
        $this->spec->assertEquals($value, $this->getField('success'));
    }

    public function thenTheErrorMessageShouldBe($value) {
        $this->spec->assertEquals($value, $this->getField('error'));
    }
}