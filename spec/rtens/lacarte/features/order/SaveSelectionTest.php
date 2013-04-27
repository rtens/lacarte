<?php
namespace spec\rtens\lacarte\features\order;

use rtens\lacarte\OrderInteractor;
use rtens\lacarte\model\Selection;
use rtens\lacarte\model\stores\SelectionStore;
use spec\rtens\lacarte\Test;
use spec\rtens\lacarte\Test_Given;
use spec\rtens\lacarte\Test_Then;
use spec\rtens\lacarte\Test_When;
use watoki\collections\Set;

/**
 * @property SaveSelectionTest_Given given
 * @property SaveSelectionTest_When when
 * @property SaveSelectionTest_Then then
 */
class SaveSelectionTest extends Test {

    function testSaveAllSelection() {
        $this->given->aNewSelection();
        $this->given->anExistingSelection();

        $this->when->iSaveTheSelections();

        $this->then->thereShouldBe_Selections(2);
    }

    function testReadSelection() {
        $this->given->theSelectionOfMenu_AndUser(54, 12);
        $this->given->theSelectionOfMenu_AndUser(54, 13);
        $this->given->theSelectionOfMenu_AndUser(52, 12);

        $this->when->iReadTheSelectionForMenu_AndUser(54, 12);

        $this->then->iShouldFindASelection();
    }

    function testNoSelection() {
        $this->given->theSelectionOfMenu_AndUser(54, 13);
        $this->given->theSelectionOfMenu_AndUser(52, 12);

        $this->when->iTryToReadTheSelectionForMenu_AndUser(54, 12);

        $this->then->anExceptionShouldBeThrownContaining('Empty result');
    }

}

/**
 * @property SaveSelectionTest test
 */
class SaveSelectionTest_Given extends Test_Given {

    /** @var Set */
    public $selections;

    function __construct(Test $test, SelectionStore $store) {
        parent::__construct($test);
        $this->selections = new Set();
        $this->store = $store;
    }

    public function aNewSelection() {
        $this->selections->put(new Selection(211, count($this->selections) + 1, 3333));
    }

    public function anExistingSelection() {
        $selection = new Selection(121, count($this->selections) + 1, 4444);
        $this->store->create($selection);
        $this->selections->put($selection);
    }

    public function theSelectionOfMenu_AndUser($menuId, $userId) {
        $selection = new Selection($userId, $menuId, 4123);
        $this->store->create($selection);
    }

}

/**
 * @property SaveSelectionTest test
 */
class SaveSelectionTest_When extends Test_When {

    /** @var Selection */
    public $selection;

    function __construct(Test $test, OrderInteractor $interactor) {
        parent::__construct($test);
        $this->interactor = $interactor;
    }

    public function iSaveTheSelections() {
        $this->interactor->saveSelections($this->test->given->selections);
    }

    public function iReadTheSelectionForMenu_AndUser($menuId, $userId) {
        $this->selection = $this->interactor->readSelectionByMenuIdAndUserId($menuId, $userId);
    }

    public function iTryToReadTheSelectionForMenu_AndUser($menuId, $userId) {
        try {
            $this->iReadTheSelectionForMenu_AndUser($menuId, $userId);
        } catch (\Exception $e) {
            $this->caught = $e;
        }
    }

}

/**
 * @property SaveSelectionTest test
 */
class SaveSelectionTest_Then extends Test_Then {

    public function thereShouldBe_Selections($count) {
        $this->test->assertEquals($count, $this->test->given->store->readAll()->count());
    }

    public function iShouldFindASelection() {
        $this->test->assertNotNull($this->test->when->selection);
    }
}