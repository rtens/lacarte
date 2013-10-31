<?php
namespace rtens\lacarte\core;

use watoki\curir\Resource;

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

    public function getUserFilesDirectory() {
        return $this->config->getUserFilesDirectory();
    }

    public function getUserAvatarUrl($user, Resource $root) {
        $file = $this->exists('avatars/' . $user->id . '.jpg') ? $user->id . '.jpg' : 'default.png';
        return $this->config->getHost() . rtrim('/' . $root->getRoute()->toString(), '/'). '/user/avatars/' . $file;
    }

    public function moveUploadedFile($from, $to) {
        return move_uploaded_file($from, $to);
    }

}
