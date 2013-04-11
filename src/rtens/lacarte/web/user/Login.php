<?php
namespace rtens\lacarte\web\user;

use rtens\lacarte\UserInteractor;
use rtens\lacarte\core\Session;
use watoki\collections\Map;
use watoki\curir\Path;
use watoki\curir\Url;
use watoki\curir\controller\Component;
use watoki\curir\controller\Module;
use watoki\factory\Factory;

class Login extends Component {

    public static $CLASS = __CLASS__;

    private $interactor;

    private $session;

    function __construct(Factory $factory, Path $route, Module $parent = null,
                         UserInteractor $interactor, Map $session) {
        parent::__construct($factory, $route, $parent);
        $this->interactor = $interactor;
        $this->session = $session;
    }

    public function doPost($email, $password) {
        $group = $this->interactor->authorizeAdmin($email, $password);

        if (!$group) {
            return array(
                'error' => 'Could not find group for given email and password',
                'email' => $email
            );
        }

        $this->session->set('group', $group->id);
        $this->session->set('isAdmin', true);
        return $this->redirect(Url::parse('../order/list.html'));
    }

}