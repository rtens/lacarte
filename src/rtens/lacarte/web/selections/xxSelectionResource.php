<?php
namespace rtens\lacarte\web\selections;

use rtens\lacarte\Presenter;
use watoki\curir\resource\DynamicResource;

class xxSelectionResource extends DynamicResource {

    public static $CLASS = __CLASS__;

    /**
     * @param int $selection
     * @param boolean $yielded
     * @return Presenter
     */
    public function doPut($selection, $yielded) {
        return new Presenter(array(
            'id' => $selection,
            'yielded' => $yielded));
    }

    protected function getPlaceholderKey() {
        return 'selection';
    }

}