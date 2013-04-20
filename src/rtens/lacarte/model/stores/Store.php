<?php
namespace rtens\lacarte\model\stores;

use rtens\lacarte\core\Database;

abstract class Store {

    public static $CLASS = __CLASS__;

    /**
     * @var \rtens\lacarte\core\Database
     */
    protected $db;

    function __construct(Database $db) {
        $this->db = $db;
    }

    /**
     * @param mixed $entity
     * @param string $tableName
     */
    protected function createEntity($entity, $tableName) {
        $columns = array();

        $refl = new \ReflectionClass($entity);
        foreach ($refl->getProperties() as $property) {
            if ($property->isStatic()) {
                continue;
            }
            $property->setAccessible(true);
            $columns[$property->getName()] = $property->getValue($entity);
        }

        $quotedColumns = implode(', ', array_map(function ($item) {
            return '"' . $item . '"';
        }, array_keys($columns)));
        $preparedColumns = implode(', ', array_map(function ($item) {
            return ':' . $item;
        }, array_keys($columns)));

        $this->db->execute("INSERT INTO $tableName ($quotedColumns) VALUES ($preparedColumns)", $columns);
        $entity->id = $this->db->getLastInsertedId();
    }

}