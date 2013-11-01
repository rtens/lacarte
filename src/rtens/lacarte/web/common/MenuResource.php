<?php
namespace rtens\lacarte\web\common;
 
use rtens\lacarte\Presenter;
use watoki\curir\resource\DynamicResource;
use watoki\dom\Element;

class MenuResource extends DynamicResource {

    public static $CLASS = __CLASS__;

    /** @var \rtens\lacarte\core\Session <- */
    protected $session;

    public function doGet() {
        $root = $this->getRoot();

        return new Presenter(array(
            'adminOnly' => $this->session->has('admin'),
            'relative' => function (Element $e) use ($root) {
                    $e->setAttribute('src', '/' . $root->getRoute()->toString() . '/common/' . $e->getAttribute('src')->getValue());
                    return true;
                }
        ));
    }

}
