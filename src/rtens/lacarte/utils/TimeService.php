<?php
namespace rtens\lacarte\utils;
 
class TimeService {

    public static $CLASS = __CLASS__;

    /**
     * @return \DateTime
     */
    public function now() {
        return new \DateTime();
    }

    /**
     * @param string $string
     * @return \DateTime
     */
    public function fromString($string) {
        $now = $this->now();
        $now->setTimestamp(strtotime($string, $now->getTimestamp()));
        return $now;
    }

    public function until(\DateTime $then) {
        return $this->now()->diff($then);
    }

}
