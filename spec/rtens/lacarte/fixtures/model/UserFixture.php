<?php
namespace spec\rtens\lacarte\fixtures\model;

use rtens\lacarte\model\Group;
use rtens\lacarte\model\stores\GroupStore;
use rtens\lacarte\model\stores\UserStore;
use rtens\lacarte\model\User;
use rtens\mockster\MockFactory;
use watoki\scrut\Fixture;

/**
 * @property GroupStore groupStore<-
 * @property UserStore store<-
 */
class UserFixture extends Fixture {

    public static $CLASS = __CLASS__;

    /** @var array|User[] */
    private $users = array();

    /** @var Group */
    private $group;

    public function givenTheGroup_WithTheAdminEmail_AndPassword($name, $email, $password) {
        $this->getGroup()->setName($name);
        $this->getGroup()->setAdminEmail($email);
        $this->getGroup()->setAdminPassword($password);

        $this->groupStore->update($this->getGroup());
    }

    public function thenThereShouldBe_Users($count) {
        $this->spec->assertCount($count, $this->store->readAll());
    }

    public function givenTheUser($name) {
        $this->givenTheUser_WithTheEmail($name, strtolower(str_replace(' ', '.', $name)) . '@example.com');
    }

    public function getGroup() {
        if (!$this->group) {
            $this->group = new Group('Test', '', '');
            $this->groupStore->create($this->group);
        }
        return $this->group;
    }

    public function getUser($name) {
        return $this->users[$name];
    }

    public function givenTheUser_WithTheEmail($name, $email) {
        $this->givenTheUser_WithTheEmail_AndKey($name, $email, 'key_' . str_replace(' ', '_', $name));
    }

    public function givenTheUser_WithTheEmail_AndKey($name, $email, $key) {
        $user = new User($this->getGroup()->id, $name, $email, $key);
        $this->store->create($user);

        $this->users[$name] = $user;
    }

    public function thenThereShouldBeAUserWithTheName_TheEmailAndTheKey($name, $email, $key) {
        foreach ($this->store->readAll() as $user) {
            if ($user->getName() == $name && $user->getEmail() == $email && $user->getKey() == $key) {
                return;
            }
        }
        $this->spec->fail('User does not exist');
    }

    public function thenThereShouldBeAUserWithTheName_TheEmail($name, $email) {
        foreach ($this->store->readAll() as $user) {
            if ($user->getName() == $name && $user->getEmail() == $email) {
                return;
            }
        }
        $this->spec->fail("User with name [$name] and email [$email] does not exist");
    }

    public function thenThereShouldBeAUserWithTheTheName($name) {
        foreach ($this->store->readAll() as $user) {
            if ($user->getName() == $name) {
                return;
            }
        }
        $this->spec->fail('User does not exist');
    }

    public function given_WasDeleted($string) {
        $this->store->delete($this->getUser($string));
    }

}