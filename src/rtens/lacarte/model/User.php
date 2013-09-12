<?php
namespace rtens\lacarte\model;

class User {

    public $id;

    private $groupId;

    private $name;

    private $email;

    private $key;

    function __construct($groupId, $name, $email, $key) {
        $this->groupId = $groupId;
        $this->name = $name;
        $this->email = $email;
        $this->key = $key;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = strtolower($email);
    }

    public function getGroupId() {
        return $this->groupId;
    }

    public function setGroupId($groupId) {
        $this->groupId = $groupId;
    }

    public function getKey() {
        return $this->key;
    }

}