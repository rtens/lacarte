<?php
namespace rtens\lacarte\utils;

class KeyGenerator {

    static $CLASS = __CLASS__;

    public function generateUnique() {
        return md5(uniqid());
    }
}