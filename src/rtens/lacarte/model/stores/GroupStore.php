<?php
namespace rtens\lacarte\model\stores;
 
use rtens\lacarte\core\Database;
use rtens\lacarte\model\Group;

class GroupStore {

    public static $CLASS = __CLASS__;

    function __construct(Database $db) {
        $this->db = $db;
    }

    public function create(Group $group) {
        $columns = array();

        $refl = new \ReflectionClass($group);
        foreach ($refl->getProperties() as $property) {
            if ($property->isStatic()) {
                continue;
            }
            $property->setAccessible(true);
            $columns[$property->getName()] = $property->getValue($group);
        }

        $quotedColumns = implode(', ', array_map(function ($item) {return '"' . $item . '"';}, array_keys($columns)));
        $preparedColumns = implode(', ', array_map(function ($item) {return ':' . $item;}, array_keys($columns)));

        $this->db->execute("INSERT INTO groups ($quotedColumns) VALUES ($preparedColumns)", $columns);
    }

    public function readByEmailAndPassword($email, $password) {
        return $this->inflate($this->db->readOne("SELECT * FROM groups WHERE adminEmail = ? AND adminPassword = ?",
            array($email, $password)));
    }

    private function inflate($row) {
        return new Group($row['name'], $row['adminEmail'], $row['adminPassword']);
    }

}
