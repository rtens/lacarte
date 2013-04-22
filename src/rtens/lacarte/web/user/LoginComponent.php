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
use watoki\tempan\Renderer;

class LoginComponent extends Component {

    public static $CLASS = __CLASS__;

    private $interactor;

    private $session;

    function __construct(Factory $factory, Path $route, Module $parent = null,
                         UserInteractor $interactor, Session $session) {
        parent::__construct($factory, $route, $parent);
        $this->interactor = $interactor;
        $this->session = $session;
    }

    public function doLoginAdmin($email, $password) {
        $group = $this->interactor->authorizeAdmin($email, $password);

        if (!$group) {
            return array(
                'error' => 'Could not find group for given email and password',
                'email' => $email
            );
        }

        $this->session->set('group', $group->id);
        $this->session->set('isAdmin', true);
        return $this->redirectToList();
    }

    public function doGet() {
        if ($this->session->has('group')) {
            return $this->redirectToList();
        }
        return array(
            'error' => null
        );
    }

    private function redirectToList() {
        return $this->redirect(Url::parse('../order/list.html'));
    }

    public function doLogout() {
        $this->session->remove('group');
        $this->session->remove('isAdmin');
        $this->session->remove('key');

        return $this->redirect(Url::parse('login.html'));
    }

    public function doPost($key) {
        $user = $this->interactor->authorizeUser($key);
        if (!$user) {
            return array(
                'error' => 'You entered an invalid key'
            );
        }

        $this->session->set('key', $user->getKey());
        $this->session->set('group', $user->getGroupId());

        return $this->redirectToList();
    }

}