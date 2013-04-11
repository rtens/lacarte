<?php
namespace spec\rtens\lacarte;

use rtens\lacarte\UserInteractor;
use rtens\lacarte\model\Group;
use rtens\lacarte\model\stores\GroupStore;

/**
 * @property AuthorizeAdminTest_Given given
 * @property AuthorizeAdminTest_When when
 * @property AuthorizeAdminTest_Then then
 */
class AuthorizeAdminTest extends Test {

    function testCorrectCredentials() {
        $this->given->theGroup_WithAdminEmail_AndPassword('TestGroup', 'john', 'asd');

        $this->when->iTryToLogInWith('john', 'asd');

        $this->then->iShouldBeAuthorizedForTheGroup('TestGroup');
    }

    function testIncorrectCredentials() {
        $this->given->theGroup_WithAdminEmail_AndPassword('TestGroup', 'john', 'asd');

        $this->when->iTryToLogInWith('john', 'bla');

        $this->then->iShouldNotBeAuthorized();
    }

}

/**
 * @property AuthorizeAdminTest test
 */
class AuthorizeAdminTest_Given extends Test_Given {

    /**
     * @var GroupStore
     */
    private $groupStore;

    function __construct(Test $test) {
        $this->groupStore = $test->factory->getInstance(GroupStore::$CLASS);
    }

    public function theGroup_WithAdminEmail_AndPassword($groupName, $email, $password) {
        $group = new Group($groupName, $email, $password);
        $this->groupStore->create($group);
    }
}

/**
 * @property AuthorizeAdminTest test
 */
class AuthorizeAdminTest_When extends Test_When {

    /**
     * @var Group
     */
    public $authorizedGroup;

    /**
     * @var UserInteractor
     */
    private $userInteractor;

    function __construct(Test $test) {
        parent::__construct($test);
        $this->userInteractor = $test->factory->getInstance(UserInteractor::$CLASS);
    }

    public function iTryToLogInWith($email, $password) {
        $this->authorizedGroup = $this->userInteractor->authorizeAdmin($email, $password);
    }
}

/**
 * @property AuthorizeAdminTest test
 */
class AuthorizeAdminTest_Then extends Test_Then {

    public function iShouldBeAuthorizedForTheGroup($groupName) {
        $this->test->assertNotNull($this->test->when->authorizedGroup);
        $this->test->assertEquals($groupName, $this->test->when->authorizedGroup->getName());
    }

    public function iShouldNotBeAuthorized() {
        $this->test->assertNull($this->test->when->authorizedGroup);
    }
}