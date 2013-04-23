<?php
namespace rtens\lacarte\model;
 
class Dish {

    public $id;

    private $menuId;

    private $text;

    function __construct($menuId, $text) {
        $this->menuId = $menuId;
        $this->text = $text;
    }

    public function getMenuId() {
        return $this->menuId;
    }

    public function getText() {
        return $this->text;
    }

    public function setText($text) {
        $this->text = $text;
    }

}
