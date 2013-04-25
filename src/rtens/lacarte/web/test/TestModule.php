<?php
namespace rtens\lacarte\web\test;
 
use watoki\scrut\web\ScrutModule;

class TestModule extends ScrutModule {

    public static $CLASS = __CLASS__;

    public function getComponentTestDirectory() {
        return 'spec/rtens/lacarte/web';
    }

}
