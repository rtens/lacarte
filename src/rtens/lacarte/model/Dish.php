<?php
namespace rtens\lacarte\model;
 
class Dish {

    const LANG_ENGLISH = 'en';
    const LANG_GERMAN = 'de';

    public $id;

    private $menuId;

    private $text;

    function __construct($menuId, $text) {
        $this->menuId = $menuId;
        $this->text = $text;
    }

    public function getMenuId() {
        return $this->menuId;
    }

    public function getText() {
        return $this->text;
    }

    public function setText($text) {
        $this->text = $text;
    }

    public function getTextIn($lang) {
        if (strpos($this->getText(), '/')) {
            $langs = explode('/', $this->getText());
            return $lang == self::LANG_GERMAN ? $langs[0] : $langs[1];
        }
        return $this->text;
    }

}
