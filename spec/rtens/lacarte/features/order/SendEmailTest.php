<?php
namespace spec\rtens\lacarte\features\order;

use rtens\lacarte\OrderInteractor;
use rtens\lacarte\model\Group;
use rtens\lacarte\model\Menu;
use rtens\lacarte\model\Order;
use rtens\lacarte\model\Selection;
use rtens\lacarte\model\User;
use rtens\lacarte\model\stores\GroupStore;
use rtens\lacarte\model\stores\MenuStore;
use rtens\lacarte\model\stores\SelectionStore;
use rtens\lacarte\model\stores\UserStore;
use rtens\lacarte\utils\MailService;
use spec\rtens\lacarte\Test;
use spec\rtens\lacarte\Test_Given;
use spec\rtens\lacarte\Test_Then;
use spec\rtens\lacarte\Test_When;

/**
 * @property SendEmailTest_Given given
 * @property SendEmailTest_When when
 * @property SendEmailTest_Then then
 */
class SendEmailTest extends Test {

    function testSendToAll() {
        $this->setUpGroupAndUsers();

        $this->when->iSendAnEmailToAllForOrder('Test');

        $this->then->_EmailsShouldBeSent(2);
        $this->then->anEmailShouldBeSentTo_From_WithTheSubject_AndTheBody('max@example.de', 'test@group.de',
            'Choose, Max', 'Max: http://lacarte/order/select.html?order=42&key=max123');
        $this->then->anEmailShouldBeSentTo_From_WithTheSubject_AndTheBody('karl@example.de', 'test@group.de',
            'Choose, Karl', 'Karl: http://lacarte/order/select.html?order=42&key=oi321');
    }

    function testSendOnlyToOnesWithoutSelection() {
        $this->setUpGroupAndUsers();

        $this->given->_HasASelectionForOrder('Karl Marx', 'Test');

        $this->when->iSendAnEmailToUsersWithoutSelectionForOrder('Test');

        $this->then->_EmailsShouldBeSent(1);
        $this->then->anEmailShouldBeSentTo_From_WithTheSubject_AndTheBody('max@example.de', 'test@group.de',
            'Choose, Max', 'Max: http://lacarte/order/select.html?order=42&key=max123');
    }

    private function setUpGroupAndUsers() {
        $this->given->theGroup_WithTheAdminEmail('Test Group', 'test@group.de');
        $this->given->theOrder_WithId('Test', 42);
        $this->given->theUser_WithTheEmail_AndTheKey('Max', 'max@example.de', 'max123');
        $this->given->theUser_WithTheEmail_AndTheKey('Karl Marx', 'karl@example.de', 'oi321');

        $this->given->theSubjectIs('Choose, {name}');
        $this->given->theBodyIs('{name}: {url}');
    }
}

class SendEmailTest_Given extends Test_Given {

    /** @var Order[] */
    public $orders = array();
    public $subject;
    public $body;

    /** @var Group */
    public $group;

    /** @var User[] */
    public $users = array();

    function __construct(Test $test, UserStore $userStore, GroupStore $groupStore,
                         SelectionStore $selectionStore, MenuStore $menuStore) {
        parent::__construct($test);
        $this->userStore = $userStore;
        $this->groupStore = $groupStore;
        $this->selectionStore = $selectionStore;
        $this->menuStore = $menuStore;
    }

    public function theGroup_WithTheAdminEmail($name, $email) {
        $this->group = new Group($name, $email, '');
        $this->groupStore->create($this->group);
    }

    public function theUser_WithTheEmail_AndTheKey($name, $email, $key) {
        $this->users[$name] = new User($this->group->id, $name, $email, $key);
        $this->userStore->create($this->users[$name]);
    }

    public function theSubjectIs($string) {
        $this->subject = $string;
    }

    public function theBodyIs($string) {
        $this->body = $string;
    }

    public function theOrder_WithId($name, $id) {
        $this->orders[$name] = new Order($this->group->id, $name, new \DateTime());
        $this->orders[$name]->id = $id;
    }

    public function _HasASelectionForOrder($user, $order) {
        $menu = new Menu($this->orders[$order]->id, new \DateTime());
        $this->menuStore->create($menu);
        $this->selectionStore->create(new Selection($this->users[$user]->id, $menu->id, 0));
    }
}

/**
 * @property SendEmailTest test
 * @property OrderInteractor orderInteractor
 */
class SendEmailTest_When extends Test_When {

    function __construct(Test $test) {
        parent::__construct($test);
        $this->mailService = $test->mf->createMock(MailService::$CLASS);
        $this->orderInteractor = $test->factory->getInstance(OrderInteractor::$CLASS, array(
            'mailService' => $this->mailService
        ));
    }

    public function iSendAnEmailToAllForOrder($name) {
        $g = $this->test->given;
        $this->orderInteractor->sendMail($g->orders[$name], $g->subject, $g->body);
    }

    public function iSendAnEmailToUsersWithoutSelectionForOrder($name) {
        $g = $this->test->given;
        $this->orderInteractor->sendMail($g->orders[$name], $g->subject, $g->body, true);
    }
}

/**
 * @property SendEmailTest test
 */
class SendEmailTest_Then extends Test_Then {

    public function _EmailsShouldBeSent($int) {
        $this->test->assertEquals($int, $this->getSendMethod()->getCalledCount());
    }

    public function anEmailShouldBeSentTo_From_WithTheSubject_AndTheBody($email, $from, $subject, $body) {
        foreach ($this->getSendMethod()->getCalledArguments() as $arguments) {
            if (in_array($email, $arguments)) {
                $this->test->assertEquals($from, $arguments['from']);
                $this->test->assertEquals($subject, $arguments['subject']);
                $this->test->assertEquals($body, $arguments['body']);
                return;
            }
        }
        $this->test->fail('No email sent to ' . $email);
    }

    private function getSendMethod() {
        return $this->test->when->mailService->__mock()->method('send');
    }
}