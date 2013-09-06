<?php
namespace rtens\lacarte\model\stores;

use rtens\lacarte\model\Group;
use rtens\lacarte\model\User;
use watoki\collections\Liste;

class UserStore extends Store {

    static $CLASS = __CLASS__;

    public function create(User $user) {
        $this->createEntity($user, 'users');
    }

    public function update(User $user) {
        $this->updateEntity($user, 'users');
    }

    /**
     * @param string $email
     * @return User
     */
    public function readByEmail($email) {
        return $this->inflate($this->db->readOne("SELECT * FROM users WHERE email = ?",
            array($email)));
    }

    /**
     * @param string $key
     * @return User
     */
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

    /**
     * @return Liste|User[]
     */
    public function readAll() {
        return $this->inflateAll($this->db->readAll("SELECT * FROM users ORDER BY name ASC"), new Liste());
    }

    /**
     * @param Group $group
     * @return Liste|User[]
     */
    public function readAllByGroup(Group $group) {
        return $this->inflateAll($this->db->readAll('SELECT * FROM users WHERE groupId = ? ORDER BY name',
            array($group->id)), new Liste());
    }

    /**
     * @param int $id
     * @return User
     */
    public function readById($id) {
        return $this->inflate($this->db->readOne('SELECT * FROM users WHERE id = ?', array($id)));
    }

    public function delete(User $user) {
        $this->deleteEntity($user, 'users');
    }

}