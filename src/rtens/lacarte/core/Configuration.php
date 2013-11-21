<?php
namespace rtens\lacarte\core;


class Configuration {

    public static $CLASS = __CLASS__;

    private $rootDir;

    function __construct($rootDir) {
        $this->rootDir = $rootDir;
    }

    /**
     * @return string
     * @see http://www.php.net/manual/de/pdo.drivers.php
     */
    function getPdoDataSourceName() {
        return 'sqlite:' . $this->rootDir . '/usr/db.sq3';
    }

    /**
     * @return string|null
     */
    function getPdoUser() {
        return null;
    }

    /**
     * @return string|null
     */
    function getPdoPassword() {
        return null;
    }

    /**
     * @return string e.g. 'http://my.page.com'
     */
    function getHost() {
        return 'http://localhost';
    }

    /**
     * @return string
     */
    function getApiToken() {
        return 'token';
    }

    /**
     * @return string
     */
    function getUserFilesDirectory() {
        return $this->rootDir . '/usr/files';
    }

}