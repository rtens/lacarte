<?php
namespace rtens\lacarte\web\user;
 
use rtens\lacarte\UserInteractor;
use rtens\lacarte\core\Session;
use rtens\lacarte\model\Group;
use rtens\lacarte\model\User;
use rtens\lacarte\web\DefaultComponent;
use rtens\lacarte\web\common\MenuComponent;
use watoki\curir\Path;
use watoki\curir\Url;
use watoki\curir\controller\Module;
use watoki\factory\Factory;

class ListComponent extends DefaultComponent {

    public static $CLASS = __CLASS__;

    public function doGet() {
        if (!$this->isAdmin()) {
            return $this->redirect(Url::parse('../order/list.html'));
        }

        return $this->assembleModel();
    }

    public function doPost($name, $email) {
        if (!$this->isLoggedIn()) {
            return $this->redirect(Url::parse('login.html'));
        }

        if (!$this->isAdmin()) {
            return $this->assembleModel(array(
                'error' => 'Access denied. Must be administrator.'
            ));
        }

        try {
            $groupId = $this->session->get('admin');
            $this->userInteractor->createUser($groupId, $name, $email);

            return $this->assembleModel(array(
                'success' => "The user $name was created."
            ));
        } catch (\Exception $e) {
            return $this->assembleModel(array(
                'error' => $e->getMessage(),
                'email' => array('value' => $email),
                'name' => array('value' => $name)
            ));
        }
    }

    protected function assembleModel($model = array()) {
        return parent::assembleModel(array_merge(array(
            'user' => $this->assembleUsers(),
            'error' => null,
            'success' => null
        ), $model));
    }

    private function assembleUsers() {
        $users = array();
        foreach ($this->userInteractor->readAll() as $user) {
            /** @var User $user */
            $users[] = array(
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'key' => $user->getKey(),
                'editAction' => array(
                    'href' => 'list.html?action=edit&id=' . $user->id
                ),
                'deleteAction' => array(
                    'href' => 'list.html?action=delete&id=' . $user->id
                )
            );
        }
        return $users;
    }

}
