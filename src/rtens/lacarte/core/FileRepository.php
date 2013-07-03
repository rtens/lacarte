<?php
namespace rtens\lacarte\core;
 
class FileRepository {

    public static $CLASS = __CLASS__;

    private $config;

    function __construct(Configuration $config) {
        $this->config = $config;
    }

    public function exists($file) {
        return file_exists($this->getFullPath($file));
    }

    public function getFullPath($file) {
        return $this->config->getUserFilesDirectory() . '/' . $file;
    }

}
