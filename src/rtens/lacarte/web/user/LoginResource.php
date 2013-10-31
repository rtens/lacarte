<?php
namespace rtens\lacarte\web\user;

use rtens\lacarte\Presenter;
use rtens\lacarte\web\DefaultResource;
use watoki\curir\http\Url;
use watoki\curir\responder\Redirecter;

class LoginResource extends DefaultResource {

    public static $CLASS = __CLASS__;

    protected function requiresLogin() {
        return false;
    }

    protected function assembleModel($model = array()) {
        return array_merge(array(
            'error' => null
        ), $model);
    }

    public function doLoginAdmin($email, $password) {
        $group = $this->userInteractor->authorizeAdmin($email, $password);

        if (!$group) {
            return new Presenter(array(
                'error' => 'Could not find group for given email and password',
                'email' => array('value' => $email)
            ));
        }

        $this->session->set('admin', $group->id);
        return $this->redirectToList();
    }

    public function doGet() {
        if ($this->isLoggedIn()) {
            return $this->redirectToList();
        }
        return new Presenter($this->assembleModel());
    }

    private function redirectToList() {
        return new Redirecter(Url::parse('../order/list.html'));
    }

    public function doLogout() {
        $this->session->remove('admin');
        $this->session->remove('key');

        return new Redirecter(Url::parse('login.html'));
    }

    public function doPost($key) {
        $user = $this->userInteractor->authorizeUser($key);

        try {
            $this->login($key);
        } catch (\Exception $e) {
            return new Presenter(array(
                'error' => 'You entered an invalid key',
                'key' => array('value' => $key)
            ));
        }

        $this->session->set('key', $user->getKey());
        return $this->redirectToList();
    }

}