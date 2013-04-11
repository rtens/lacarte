<?php
namespace rtens\lacarte;
 
use rtens\lacarte\core\NotFoundException;
use rtens\lacarte\model\stores\GroupStore;

class UserInteractor {

    public static $CLASS = __CLASS__;

    /**
     * @var model\stores\GroupStore
     */
    private $store;

    function __construct(GroupStore $store) {
        $this->store = $store;
    }

    public function authorizeAdmin($email, $password) {
        try {
            return $this->store->readByEmailAndPassword($email, $password);
        } catch (NotFoundException $e) {
            return null;
        }
    }

}
