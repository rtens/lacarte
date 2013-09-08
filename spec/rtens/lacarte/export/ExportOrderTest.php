<?php
namespace spec\rtens\lacarte\export;

use spec\rtens\lacarte\TestCase;

class ExportOrderTest extends TestCase {

    function _testNotAdmin() {
        $this->given->anOrder_With_MenusEach_Dishes('Test', 3, 2);

        $this->when->iGetAndDishesExportForTheOrder();

        $this->then->iShouldBeRedirectedTo('../order/list.html');
    }

    function _testNoSelections() {
        $this->given->iAmLoggedInAsAdmin();
        $this->given->anOrder_With_MenusEach_Dishes('Test', 3, 2);
        $this->given->dish_OfMenu_Is(1, 1, '1A');
        $this->given->dish_OfMenu_Is(2, 1, '1B');
        $this->given->dish_OfMenu_Is(1, 2, '2A');
        $this->given->dish_OfMenu_Is(2, 2, '2B');
        $this->given->dish_OfMenu_Is(1, 3, '3A');
        $this->given->dish_OfMenu_Is(2, 3, '3B');

        $this->when->iGetAndDishesExportForTheOrder();

        $this->then->_shouldHaveTheSize('content', 6);
        $this->then->_shouldBe('content/0/date', '2000-01-03');
        $this->then->_shouldBe('content/0/dish', '1A');
        $this->then->_shouldBe('content/0/sum', '0');
    }

    function _testSelections() {
        $this->given->iAmLoggedInAsAdmin();
        $this->given->anOrder_With_MenusEach_Dishes('Test', 1, 2);
        $this->given->dish_OfMenu_Is(1, 1, '1A');
        $this->given->dish_OfMenu_Is(2, 1, '1B');

        $this->given->theUser('Tom');
        $this->given->theUser('Jerry');
        $this->given->theUser('Max');
        $this->given->theUser('Moritz');

        $this->given->_SelectedDish_ForMenu('Tom', 1, 1);
        $this->given->_SelectedDish_ForMenu('Jerry', 2, 1);
        $this->given->_SelectedDish_ForMenu('Max', 1, 1);
        $this->given->_SelectedDish_ForMenu('Moritz', 0, 1);

        $this->when->iGetAndDishesExportForTheOrder();

        $this->then->_shouldHaveTheSize('content', 2);
        $this->then->_shouldBe('content/0/dish', '1A');
        $this->then->_shouldBe('content/0/sum', 2);
        $this->then->_shouldBe('content/0/by', 'Tom, Max');
        $this->then->_shouldBe('content/1/dish', '1B');
        $this->then->_shouldBe('content/1/sum', 1);
        $this->then->_shouldBe('content/1/by', 'Jerry');
    }

    function _testSelectionWithDeletedUser() {
        $this->given->iAmLoggedInAsAdmin();
        $this->given->anOrder_With_MenusEach_Dishes('Test', 1, 1);

        $this->given->theUser('Tom');
        $this->given->theUser('Jerry');
        $this->given->_SelectedDish_ForMenu('Tom', 1, 1);
        $this->given->_SelectedDish_ForMenu('Jerry', 1, 1);

        $this->given->_wasDeleted('Tom');

        $this->when->iGetAndDishesExportForTheOrder();

        $this->then->_shouldHaveTheSize('content', 1);
        $this->then->_shouldBe('content/0/by', 'Deleted, Jerry');
    }

}