<?php
namespace rtens\lacarte\model;
 
class Group {

    private $name;

    private $adminEmail;

    private $adminPassword;

    function __construct($name, $adminEmail, $adminPassword) {
        $this->name = $name;
        $this->adminEmail = $adminEmail;
        $this->adminPassword = $adminPassword;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getAdminEmail() {
        return $this->adminEmail;
    }

    public function setAdminEmail($adminEmail) {
        $this->adminEmail = $adminEmail;
    }

    public function setAdminPassword($adminPassword) {
        $this->adminPassword = $adminPassword;
    }

}
