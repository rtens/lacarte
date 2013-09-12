<?php
namespace spec\rtens\lacarte\specs\order;

use rtens\mockster\MockFactory;
use spec\rtens\lacarte\fixtures\component\order\SelectionsComponentFixture;
use spec\rtens\lacarte\fixtures\model\OrderFixture;
use spec\rtens\lacarte\fixtures\model\UserFixture;
use spec\rtens\lacarte\fixtures\service\MailFixture;
use spec\rtens\lacarte\fixtures\service\SessionFixture;
use spec\rtens\lacarte\TestCase;

/**
 * @property OrderFixture order
 * @property SessionFixture session
 * @property UserFixture user
 * @property MailFixture mail
 * @property SelectionsComponentFixture component
 */
class SendMailTest extends TestCase {

    function testNotAdmin() {
        $this->order->givenTheOrder('Test Order');
        $this->component->givenIOpenThePageForOrder('Test Order');
        $this->component->givenIHaveEnteredTheSubject('Hello');
        $this->component->givenIHaveEnteredTheBody('What up?');

        $this->component->whenISendTheMail();

        $this->component->thenIShouldBeRedirectedTo('list.html');
    }

    function testSendMail() {
        $this->session->givenIAmLoggedInAsAdmin();
        $this->order->givenTheOrder('Test Order');
        $this->component->givenIOpenThePageForOrder('Test Order');
        $this->component->givenIHaveEnteredTheSubject('Hello');
        $this->component->givenIHaveEnteredTheBody('What up?');

        $this->user->givenTheUser('Homer');
        $this->user->givenTheUser('Marge');

        $this->component->whenISendTheMail();

        $this->mail->then_EmailsShouldBeSent(2);
        $this->mail->thenAMailShouldBeSentTo_WithTheSubject_AndTheBody('Homer', 'Hello', 'What up?');
        $this->mail->thenAMailShouldBeSentTo_WithTheSubject_AndTheBody('Marge', 'Hello', 'What up?');
        $this->component->thenTheSuccessMessageShouldBe('Email was sent to users');
        $this->component->thenThereShouldBeNoErrorMessage();
    }

    function testOnlySendToUsersWithoutSelection() {
        $this->session->givenIAmLoggedInAsAdmin();

        $this->order->givenAnOrder_With_MenusEach_Dishes('Test Order', 1, 1);
        $this->order->givenDish_OfMenu_OfThisOrderIs(1, 1, 'A');

        $this->user->givenTheUser('Marge Simpson');
        $this->user->givenTheUser('Homer Simpson');
        $this->user->givenTheUser('Lisa');
        $this->order->given_SelectedDish_ForMenu_OfOrder('Homer Simpson', 'A', 1, 'Test Order');

        $this->component->givenIOpenThePageForOrder('Test Order');
        $this->component->givenIHaveEnteredTheSubject('Hello {name}');
        $this->component->givenIHaveEnteredTheBody('Order now: {url}');
        $this->component->givenIHaveSelectedToSendTheEmailOnlyToUsersWithoutSelection();

        $this->component->whenISendTheMail();

        $this->component->thenTheSuccessMessageShouldBe('Email was sent to users without selections');

        $this->mail->then_EmailsShouldBeSent(2);
        $this->mail->thenAMailShouldBeSentTo_WithTheSubject_AndTheBody('Marge Simpson', 'Hello Marge',
            'Order now: http://lacarte/order/select.html?order=1&key=key_Marge_Simpson');
        $this->mail->thenAMailShouldBeSentTo_WithTheSubject_AndTheBody('Lisa', 'Hello Lisa',
            'Order now: http://lacarte/order/select.html?order=1&key=key_Lisa');
    }

    function testEmptyField() {
        $this->session->givenIAmLoggedInAsAdmin();
        $this->order->givenTheOrder('Test Order');
        $this->component->givenIOpenThePageForOrder('Test Order');
        $this->component->givenIHaveEnteredTheSubject('Hello');
        $this->component->givenIHaveEnteredTheBody('');
        $this->component->givenIHaveSelectedToSendTheEmailOnlyToUsersWithoutSelection();

        $this->user->givenTheUser('Homer');

        $this->component->whenISendTheMail();

        $this->mail->then_EmailsShouldBeSent(0);
        $this->component->thenThereShouldBeNoSuccessMessage();
        $this->component->thenTheErrorMessageShouldBe('Please fill out subject and body to send an email');

        $this->component->thenTheSubjectFieldShouldContain('Hello');
        $this->component->thenTheCheckboxToSendOnlyToUsersWithoutSelectionShouldBeChecked();
    }

    function testErrorWhileSending() {
        $this->session->givenIAmLoggedInAsAdmin();
        $this->order->givenTheOrder('Test Order');
        $this->component->givenIOpenThePageForOrder('Test Order');
        $this->component->givenIHaveEnteredTheSubject('Hello');
        $this->component->givenIHaveEnteredTheBody('What up?');

        $this->user->givenTheUser('Homer');
        $this->user->givenTheUser('Marge');

        $this->mail->givenAnError_OccursWhileSendingTheMail('Some bad thing');

        $this->component->whenISendTheMail();

        $this->component->thenTheErrorMessageShouldBe('Some bad thing');
        $this->component->thenTheSubjectFieldShouldContain('Hello');
        $this->component->thenTheBodyFieldShouldContain('What up?');
        $this->component->thenTheCheckboxToSendOnlyToUsersWithoutSelectionShouldNotBeChecked();
    }

}