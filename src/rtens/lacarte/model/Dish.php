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
            return trim($langs[$lang == self::LANG_GERMAN ? 0 : 1]);
        }
        return trim($this->text);
    }

}
