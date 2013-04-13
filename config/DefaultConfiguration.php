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
}