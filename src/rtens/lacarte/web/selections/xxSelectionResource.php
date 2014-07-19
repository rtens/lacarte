<?php
namespace rtens\lacarte\web\selections;

use rtens\lacarte\core\NotFoundException;
use rtens\lacarte\OrderInteractor;
use rtens\lacarte\Presenter;
use watoki\curir\resource\DynamicResource;

class xxSelectionResource extends DynamicResource {

    public static $CLASS = __CLASS__;

    /** @var OrderInteractor <- */
    public $interactor;

    /**
     * @param int $selection
     * @param boolean $yielded
     * @return Presenter
     */
    public function doPut($selection, $yielded) {
        try {
            $this->interactor->yieldSelection($selection, $yielded);
            $model = array();
        } catch (NotFoundException $e) {
            $model = array(
                'error' => "Selection with ID [$selection] not found."
            );
        }

        return new Presenter($this, $model);
    }

    protected function getPlaceholderKey() {
        return 'selection';
    }

}