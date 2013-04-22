<?php
namespace rtens\lacarte\web\order;
 
use rtens\lacarte\web\DefaultComponent;

class ListComponent extends DefaultComponent {

    public static $CLASS = __CLASS__;

    public function doGet() {
        return $this->assembleModel();
    }

}
