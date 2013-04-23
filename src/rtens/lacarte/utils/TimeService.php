<?php
namespace rtens\lacarte\utils;
 
class TimeService {

    public static $CLASS = __CLASS__;

    public function now() {
        return new \DateTime();
    }

    public function fromString($string) {
        $now = $this->now();
        $now->setTimestamp(strtotime($string, $now->getTimestamp()));
        return $now;
    }

}
