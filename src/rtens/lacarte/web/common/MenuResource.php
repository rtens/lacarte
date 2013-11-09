<?php
namespace rtens\lacarte\web\common;
 
use rtens\lacarte\Presenter;
use rtens\lacarte\WebResource;
use watoki\curir\resource\DynamicResource;
use watoki\dom\Element;

class MenuResource extends DynamicResource {

    public static $CLASS = __CLASS__;

    /** @var \rtens\lacarte\core\Session <- */
    protected $session;

    public function doGet() {
        return new Presenter(array(
            'adminOnly' => $this->session->has('admin'),
            'relative' => function (Element $e) {
                    foreach (array('src', 'href') as $attribute) {
                        if ($e->getAttribute($attribute)) {
                            $e->setAttribute($attribute, $this->getAncestor(WebResource::$CLASS)
                                ->getUrl('common/' . $e->getAttribute($attribute)->getValue()));
                        }
                    }
                    return true;
                }
        ));
    }

}
