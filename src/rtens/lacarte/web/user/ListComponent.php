<?php
namespace rtens\lacarte\web\user;
 
use rtens\lacarte\web\DefaultComponent;
use rtens\lacarte\web\common\MenuComponent;

class ListComponent extends DefaultComponent {

    public static $CLASS = __CLASS__;

    public function doGet() {
        return array(
            "menu" => $this->subComponent(MenuComponent::$CLASS)
        );
    }

}
