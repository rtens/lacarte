<?php
namespace spec\rtens\lacarte\features\user;

use rtens\lacarte\UserInteractor;
use rtens\lacarte\model\Group;
use rtens\lacarte\model\User;
use rtens\lacarte\model\stores\GroupStore;
use rtens\lacarte\model\stores\UserStore;
use rtens\lacarte\utils\KeyGenerator;
use spec\rtens\lacarte\Test;
use spec\rtens\lacarte\Test_Given;
use spec\rtens\lacarte\Test_Then;
use spec\rtens\lacarte\Test_When;
use spec\rtens\lacarte\web\ComponentTest;
use spec\rtens\lacarte\web\ComponentTest_Given;
use spec\rtens\lacarte\web\ComponentTest_Then;
use spec\rtens\lacarte\web\ComponentTest_When;

/**
 * @property UpdateUserTest_Given given
 * @property UpdateUserTest_When when
 * @property UpdateUserTest_Then then
 */
class UpdateUserTest extends ComponentTest {

    public function setUp() {
        parent::setUp();
    }

    public function testNoChange() {
        $this->given->theUser_WithTheEmail('Homer', 'donoughts@burns.com');

        $this->when->iUpdateTheUser();

        $this->then->thereShouldBeOneUser();
        $this->then->theUsersNameShouldBe('Homer');
        $this->then->theUsersEmailShouldBe('donoughts@burns.com');
    }
    public function testChangeNameAndEmail() {
        $this->given->theUser_WithTheEmail('Homer', 'donoughts@burns.com');
        $this->given->iChangedTheNameTo('Bart');
        $this->given->iChangedTheEmailTo('eatmyshorts@burns.com');

        $this->when->iUpdateTheUser();

        $this->then->thereShouldBeOneUser();
        $this->then->theUsersNameShouldBe('Bart');
        $this->then->theUsersEmailShouldBe('eatmyshorts@burns.com');
    }


}

class UpdateUserTest_Given extends ComponentTest_Given {
    /**
     * @var User
     */
    public $user;

    /**
     * @var UserInteractor
     */
    public $interactor;

    /** @var UserStore */
    public $userStore;

    function __construct(Test $test) {
        parent::__construct($test);
        $this->userStore = $this->test->factory->getInstance(UserStore::$CLASS);
        $this->interactor = $this->test->factory->getInstance(UserInteractor::$CLASS);
    }

    public function theUser_WithTheEmail($name, $email) {
        $this->user = new User(1, $name, $email, 'abc');
        $this->userStore->create($this->user);
    }

    public function iChangedTheNameTo($string)
    {
        $this->user->setName($string);
    }

    public function iChangedTheEmailTo($string)
    {
        $this->user->setEmail($string);
    }

}

/**
 * @property UpdateUserTest test
 */
class UpdateUserTest_When extends ComponentTest_When {

    public function iUpdateTheUser()
    {
        /** @var UserInteractor $interactor */
        $interactor = $this->test->factory->getInstance(UserInteractor::$CLASS);
        $interactor->updateUser($this->test->given->user);
    }
}
/**
 * @property UpdateUserTest test
 */
class UpdateUserTest_Then extends ComponentTest_When {

    /**
     * @var UserStore
     */
    public $store;

    function __construct(Test $test)
    {
        parent::__construct($test);
        $this->store = $this->test->factory->getInstance(UserStore::$CLASS);
    }


    public function thereShouldBeOneUser()
    {
        $users = $this->store->readAll();
        $this->test->assertEquals(1, count($users));
    }

    public function theUsersNameShouldBe($string)
    {
        $users = $this->store->readAll();
        $this->test->assertEquals($string, $users->one()->getName());
    }

    public function theUsersEmailShouldBe($string)
    {
        $users = $this->store->readAll();
        $this->test->assertEquals($string, $users->one()->getEmail());
    }
}

