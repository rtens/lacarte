<?php
namespace rtens\lacarte\model;

class User {

    public $id;

    private $groupId;

    private $name;

    private $email;

    function __construct($groupId, $name, $email) {
        $this->groupId = $groupId;
        $this->name = $name;
        $this->email = $email;
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
        $this->email = $email;
    }

    public function getGroupId() {
        return $this->groupId;
    }

    public function setGroupId($groupId) {
        $this->groupId = $groupId;
    }

}