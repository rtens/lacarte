<?php
namespace rtens\lacarte\core;


interface Configuration {

    const Configuration = __CLASS__;

    /**
     * @return string
     * @see http://www.php.net/manual/de/pdo.drivers.php
     */
    function getPdoDataSourceName();

    /**
     * @return string|null
     */
    function getPdoUser();

    /**
     * @return string|null
     */
    function getPdoPassword();

    /**
     * @return string e.g. 'http://my.page.com'
     */
    function getHost();

    /**
     * @return string
     */
    function getApiToken();

}