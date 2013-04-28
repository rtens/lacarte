<?php
namespace spec\rtens\lacarte\web\order;

use rtens\lacarte\web\order\SelectionsComponent;
use spec\rtens\lacarte\Test;
use spec\rtens\lacarte\web\ComponentTest_Then;
use spec\rtens\lacarte\web\ComponentTest_When;

/**
 * @property SendMailTest_Given given
 * @property SendMailTest_When when
 * @property SendMailTest_Then then
 */
class SendMailTest extends OrderTest {

    function testNotAdmin() {
        $this->given->anOrder_With_MenusEach_Dishes('Test', 0, 0);
        $this->given->iHaveEnteredTheSubject('Hello');
        $this->given->iHaveEnteredTheBody('What up?');

        $this->when->iSendTheMail();

        $this->then->iShouldBeRedirectedTo('list.html');
    }

    function testSuccess() {
        $this->given->iAmLoggedInAsAdmin();
        $this->given->anOrder_With_MenusEach_Dishes('Test', 0, 0);
        $this->given->iHaveEnteredTheSubject('Hello');
        $this->given->iHaveEnteredTheBody('What up?');

        $this->when->iSendTheMail();

        $this->then->aMailShouldBeSentToTheOrderWIthSubject_AndBody('Hello', 'What up?');
        $this->then->_shouldBe('success', 'Email was sent to users');
        $this->then->_shouldBe('error', null);
    }

    function testOnlyWithout() {
        $this->given->iAmLoggedInAsAdmin();
        $this->given->anOrder_With_MenusEach_Dishes('Test', 0, 0);
        $this->given->iHaveEnteredTheSubject('Hello');
        $this->given->iHaveEnteredTheBody('What up?');
        $this->given->iHaveSelectedToSendTheEmailOnlyToUsersWithoutSelection();

        $this->when->iSendTheMail();

        $this->then->aMailShouldBeSentToTheOrderWIthSubject_AndBody('Hello', 'What up?');
        $this->then->_shouldBe('success', 'Email was sent to users without selections');
        $this->then->_shouldBe('error', null);
    }

    function testEmptyField() {
        $this->given->iAmLoggedInAsAdmin();
        $this->given->anOrder_With_MenusEach_Dishes('Test', 0, 0);
        $this->given->iHaveEnteredTheSubject('');
        $this->given->iHaveEnteredTheBody('What up?');
        $this->given->iHaveSelectedToSendTheEmailOnlyToUsersWithoutSelection();

        $this->when->iSendTheMail();

        $this->then->aNoMailShouldBeSent();
        $this->then->_shouldBe('success', null);
        $this->then->_shouldBe('error', 'Please fill out subject and body to send an email');

        $this->then->_shouldBe('email/body', 'What up?');
        $this->then->_shouldBe('email/onlyWithout/checked', "checked");
    }

    function testError() {
        $this->given->iAmLoggedInAsAdmin();
        $this->given->anOrder_With_MenusEach_Dishes('Test', 0, 0);
        $this->given->iHaveEnteredTheSubject('Hi');
        $this->given->iHaveEnteredTheBody('What up?');

        $this->given->anErrorOccursWhileSending('Some bad thing');

        $this->when->iSendTheMail();

        $this->then->_shouldBe('error', 'Some bad thing');

        $this->then->_shouldBe('email/subject/value', 'Hi');
        $this->then->_shouldBe('email/body', 'What up?');
        $this->then->_shouldBe('email/onlyWithout/checked', false);
    }

}

/**
 * @property SendMailTest test
 */
class SendMailTest_Given extends OrderTest_Given {

    public $onlyWithout = false;
    public $subject;
    public $body;

    public function iHaveEnteredTheSubject($string) {
        $this->subject = $string;
    }

    public function iHaveEnteredTheBody($string) {
        $this->body = $string;
    }

    public function iHaveSelectedToSendTheEmailOnlyToUsersWithoutSelection() {
        $this->onlyWithout = true;
    }

    public function anErrorOccursWhileSending($string) {
        $this->orderInteractor->__mock()->method('sendMail')->willThrow(new \Exception($string));
    }
}

/**
 * @property SendMailTest test
 * @property SelectionsComponent component
 */
class SendMailTest_When extends ComponentTest_When {

    function __construct(Test $test) {
        parent::__construct($test);
        $this->createDefaultComponent(SelectionsComponent::$CLASS, array(
            'orderInteractor' => $this->test->given->orderInteractor
        ));
    }

    public function iSendTheMail() {
        $g = $this->test->given;
        $this->model = $this->component->doSendMail($g->order->id, $g->subject, $g->body, $g->onlyWithout);
    }
}

/**
 * @property SendMailTest test
 */
class SendMailTest_Then extends ComponentTest_Then {

    public function aMailShouldBeSentToTheOrderWIthSubject_AndBody($subject, $body) {
        $method = $this->test->given->orderInteractor->__mock()->method('sendMail');
        $this->test->assertEquals(1, $method->getCalledCount());

        $args = $method->getCalledArgumentsAt(0);
        $this->test->assertEquals($subject, $args['subject']);
        $this->test->assertEquals($body, $args['body']);
        $this->test->assertEquals($this->test->given->order, $args['order']);
    }

    public function aNoMailShouldBeSent() {
        $this->test->assertFalse($this->test->given->orderInteractor->__mock()->method('sendMail')->wasCalled());
    }
}