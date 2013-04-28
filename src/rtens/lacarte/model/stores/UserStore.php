<?php
namespace rtens\lacarte\model\stores;

use rtens\lacarte\core\NotFoundException;
use rtens\lacarte\model\Group;
use rtens\lacarte\model\User;
use watoki\collections\Liste;

class UserStore extends Store {

    static $CLASS = __CLASS__;

    public function create(User $user) {
        $this->createEntity($user, 'users');
    }

    /**
     * @param string $email
     * @throws NotFoundException
     * @return User
     */
    public function readByEmail($email) {
        return $this->inflate($this->db->readOne("SELECT * FROM users WHERE email = ?",
            array($email)));
    }

    public function readByKey($key) {
        return $this->inflate($this->db->readOne("SELECT * FROM users WHERE \"key\" = ?",
            array($key)));
    }

    protected function inflate($row) {
        $user = new User($row['groupId'], $row['name'], $row['email'], $row['key']);
        $user->id = intval($row['id']);
        return $user;
    }

    public function isKeyExisting($key) {
        $result = $this->db->readOne('SELECT count(*) as count FROM users WHERE "key" = ?', array($key));
        return $result['count'] != 0;
    }

    public function readAll() {
        return $this->inflateAll($this->db->readAll("SELECT * FROM users ORDER BY name ASC"), new Liste());
    }

    /**
     * @param Group $group
     * @return array|\watoki\collections\Set|User[]
     */
    public function readAllByGroup(Group $group) {
        return $this->inflateAll($this->db->readAll('SELECT * FROM users WHERE groupId = ? ORDER BY name',
            array($group->id)), new Liste());
    }

}