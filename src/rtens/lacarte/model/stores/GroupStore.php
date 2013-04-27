<?php
namespace rtens\lacarte\model\stores;
 
use rtens\lacarte\core\Database;
use rtens\lacarte\model\Group;

class GroupStore extends Store {

    public static $CLASS = __CLASS__;

    public function create(Group $group) {
        $this->createEntity($group, 'groups');
    }

    public function readByEmailAndPassword($email, $password) {
        return $this->inflate($this->db->readOne("SELECT * FROM groups WHERE adminEmail = ? AND adminPassword = ?",
            array($email, $password)));
    }

    protected function inflate($row) {
        $group = new Group($row['name'], $row['adminEmail'], $row['adminPassword']);
        $group->id = intval($row['id']);
        return $group;
    }

    public function readById($id) {
        return $this->inflate($this->db->readOne("SELECT * FROM groups WHERE id = ?",
            array($id)));
    }

}
