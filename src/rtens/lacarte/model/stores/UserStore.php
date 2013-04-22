<?php
namespace rtens\lacarte\model\stores;

use rtens\lacarte\core\NotFoundException;
use rtens\lacarte\model\Group;
use rtens\lacarte\model\User;

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

    private function inflate($row) {
        $user = new User($row['groupId'], $row['name'], $row['email'], $row['key']);
        $user->id = $row['id'];
        return $user;
    }

    public function isKeyExisting($key) {
        $result = $this->db->readOne('SELECT count(*) as count FROM users WHERE "key" = ?', array($key));
        return $result['count'] != 0;
    }

    public function readAll() {
        $users = array();
        foreach ($this->db->readAll("SELECT * FROM users ORDER BY name ASC") as $row) {
            $users[] = $this->inflate($row);
        }
        return $users;
    }

}