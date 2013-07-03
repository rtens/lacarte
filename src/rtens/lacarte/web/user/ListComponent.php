<?php
namespace rtens\lacarte\web\user;
 
use rtens\lacarte\core\FileRepository;
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
use watoki\tempan\Renderer;

class ListComponent extends DefaultComponent {

    public static $CLASS = __CLASS__;

    private $files;

    function __construct(Factory $factory, Path $route, Module $parent = null,
                         UserInteractor $userInteractor, Session $session, FileRepository $files) {
        parent::__construct($factory, $route, $parent, $userInteractor, $session);
        $this->files = $files;
    }

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

    public function doDelete($user) {
        if (!$this->isAdmin()) {
            return $this->redirect(Url::parse('../order/list.html'));
        }

        $entity = new User(1, '', '', '');
        $entity->id = $user;
        $this->userInteractor->delete($entity);

        return $this->assembleModel(array(
            'success' => 'The user has been deleted'
        ));
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
                    'href' => 'list.html?action=edit&user=' . $user->id
                ),
                'deleteAction' => array(
                    'href' => 'list.html?action=delete&user=' . $user->id
                ),
                'avatar' => array(
                    'src' => $this->files->getUserAvatarUrl($user),
                    'href' => $this->files->getUserAvatarUrl($user),
                )
            );
        }
        return $users;
    }

}
