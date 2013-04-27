<?php
namespace rtens\lacarte\model\stores;

use rtens\lacarte\core\Database;
use watoki\collections\Set;

abstract class Store {

    public static $CLASS = __CLASS__;

    /**
     * @var \rtens\lacarte\core\Database
     */
    protected $db;

    abstract protected function inflate($row);

    function __construct(Database $db) {
        $this->db = $db;
    }

    protected function createEntity($entity, $tableName) {
        $columns = $this->getProperties($entity);

        $quotedColumns = implode(', ', array_map(function ($item) {
            return '"' . $item . '"';
        }, array_keys($columns)));
        $preparedColumns = implode(', ', array_map(function ($item) {
            return ':' . $item;
        }, array_keys($columns)));

        $this->db->execute("INSERT INTO $tableName ($quotedColumns) VALUES ($preparedColumns)", $columns);
        $entity->id = $this->db->getLastInsertedId();
    }

    protected function updateEntity($entity, $tableName) {
        $columns = $this->getProperties($entity);
        $preparedColumns = implode(', ', array_map(function ($key) {
            return '"' . $key . '" = :' . $key;
        }, array_keys($columns)));

        $this->db->execute("UPDATE $tableName SET " . $preparedColumns . " WHERE id = :id", $columns);
    }

    protected function deleteEntity($entity, $tableName) {
        $this->db->execute("DELETE FROM $tableName WHERE id = ?", array($entity->id));
    }

    private function getProperties($entity) {
        $columns = array();
        $refl = new \ReflectionClass($entity);
        foreach ($refl->getProperties() as $property) {
            if ($property->isStatic()) {
                continue;
            }
            $property->setAccessible(true);
            $columns[$property->getName()] = $this->serialize($property->getValue($entity));
        }
        return $columns;
    }

    protected function serialize($value) {
        if ($value instanceof \DateTime) {
            return $value->format('Y-m-d H:i:s');
        } else {
            return $value;
        }
    }

    protected function inflateAll($rows, $collection = null) {
        $entities = $collection ?: new Set();
        foreach ($rows as $row) {
            $entities[] = $this->inflate($row);
        }
        return $entities;
    }

}