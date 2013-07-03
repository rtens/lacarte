<?php
namespace cfg\rtens\lacarte;

use rtens\lacarte\core\Configuration;

class DefaultConfiguration implements Configuration {

    /**
     * @return string
     * @see http://www.php.net/manual/de/pdo.drivers.php
     */
    function getPdoDataSourceName() {
        return 'sqlite:' . __DIR__ . '/../opt/db.sq3';
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
        return __DIR__ . '/../opt/files';
    }
}