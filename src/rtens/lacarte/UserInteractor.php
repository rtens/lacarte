<?php
namespace rtens\lacarte;
 
use rtens\lacarte\core\NotFoundException;
use rtens\lacarte\model\Group;
use rtens\lacarte\model\User;
use rtens\lacarte\model\stores\GroupStore;
use rtens\lacarte\model\stores\UserStore;

class UserInteractor {

    public static $CLASS = __CLASS__;

    /**
     * @var model\stores\GroupStore
     */
    private $groupStore;

    /**
     * @var model\stores\UserStore
     */
    private $userStore;

    function __construct(GroupStore $groupStore, UserStore $userStore) {
        $this->groupStore = $groupStore;
        $this->userStore = $userStore;
    }

    public function authorizeAdmin($email, $password) {
        try {
            return $this->groupStore->readByEmailAndPassword($email, $password);
        } catch (NotFoundException $e) {
            return null;
        }
    }

    public function createUser(Group $group, $name, $email) {
        $key = md5($group->getName() . $name . $email . time());
        $user = new User($group->id, $name, $email, $key);
        $this->userStore->create($user);
        return $user;
    }

}
