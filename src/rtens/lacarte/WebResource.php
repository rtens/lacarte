<?php
namespace rtens\lacarte;
 
use watoki\curir\http\Url;
use watoki\curir\resource\Container;
use watoki\curir\responder\Redirecter;

class WebResource extends Container {

    public static $CLASS = __CLASS__;

    public function doGet() {
        return new Redirecter(Url::parse('user/login.html'));
    }

}
 