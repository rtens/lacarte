<?php
namespace rtens\lacarte\web\user;

use rtens\lacarte\web\DefaultComponent;
use watoki\curir\Path;
use watoki\curir\Url;

class LoginComponent extends DefaultComponent {

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
            return array(
                'error' => 'Could not find group for given email and password',
                'email' => $email
            );
        }

        $this->session->set('admin', $group->id);
        return $this->redirectToList();
    }

    public function doGet() {
        if ($this->isLoggedIn()) {
            return $this->redirectToList();
        }
        return $this->assembleModel();
    }

    private function redirectToList() {
        return $this->redirect(Url::parse('../order/list.html'));
    }

    public function doLogout() {
        $this->session->remove('admin');
        $this->session->remove('key');

        return $this->redirect(Url::parse('login.html'));
    }

    public function doPost($key) {
        $user = $this->userInteractor->authorizeUser($key);

        try {
            $this->login($key);
        } catch (\Exception $e) {
            return array(
                'error' => 'You entered an invalid key'
            );
        }

        $this->session->set('key', $user->getKey());
        return $this->redirectToList();
    }

}