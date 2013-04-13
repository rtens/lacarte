<?php
namespace rtens\lacarte\core;

use watoki\collections\Map;
use watoki\factory\Factory;

class Session {

    public static $CLASS = __CLASS__;

    function __construct(Factory $factory) {
        $factory->setSingleton(get_class($this), $this);
        session_start();
    }

    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public function get($key) {
        return $_SESSION[$key];
    }

    public function has($key) {
        return array_key_exists($key, $_SESSION);
    }

    public function remove($key) {
        unset($_SESSION[$key]);
    }

}