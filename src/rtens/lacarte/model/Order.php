<?php
namespace rtens\lacarte\model;
 
class Order {

    public $id;

    /** @var int */
    private $groupId;

    /** @var string */
    private $name;

    /** @var \DateTime */
    private $deadline;

    function __construct($groupId, $name, \DateTime $deadline) {
        $this->groupId = $groupId;
        $this->name = $name;
        $this->deadline = $deadline;
    }

    /**
     * @return int
     */
    public function getGroupId() {
        return $this->groupId;
    }

    /**
     * @return \DateTime
     */
    public function getDeadline() {
        return $this->deadline;
    }

    /**
     * @param \DateTime $deadline
     */
    public function setDeadline($deadline) {
        $this->deadline = $deadline;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

}
