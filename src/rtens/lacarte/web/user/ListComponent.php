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

    public function doEdit($user) {
        if (!$this->isAdmin()) {
            return $this->redirect(Url::parse('../order/list.html'));
        }
        return $this->assembleModel(array(), $user);
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

    public function doSave($name, $email, $userId, $key) {
        if (!$this->isAdmin()) {
            return $this->redirect(Url::parse('../order/list.html'));
        }
        $picture = $_FILES['picture']['name'];
        $pictureTmp = $_FILES['picture']['tmp_name'];
        if ($picture) {
            if ('jpg' != substr($picture, strrpos($picture, '.') + 1)) {
                return $this->assembleModel(array(
                    'error' => 'Only jpg-files allowed.'
                ));
            };

            $avatarPath = $this->files->getUserFilesDirectory(). '/avatars/' . $userId . '.jpg';
            if(!move_uploaded_file($pictureTmp, $avatarPath)) {
                return $this->assembleModel(array(
                    'error' => 'There was an error uploading the file, please try again!'
                ));
            }
        }
        $groupId = $this->session->get('admin');
        $this->userInteractor->updateUser($groupId, $name, $email, $userId, $key);
        return $this->assembleModel(array(
            'success' => 'The user has been updated'
        ));
    }

    protected function assembleModel($model = array(), $userId = null) {
        $editing = $this->assembleEditing($userId);
        return parent::assembleModel(array_merge(array(
            'user' => $this->assembleUsers(),
            'error' => null,
            'success' => null,
            'notEditing' => !$editing,
            'editing' => $editing
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
                    'src' => $this->files->getUserAvatarUrl($user, $this->getRoot()),
                    'href' => $this->files->getUserAvatarUrl($user, $this->getRoot()),
                )
            );
        }
        return $users;
    }

    private function assembleEditing($userId) {
        if (!$userId) {
            return null;
        }
        $user = $this->userInteractor->readById($userId);
        return array(
            'name' => array(
                'value' => $user->getName()
            ),
            'email' => array(
                'value' => $user->getEmail(),
            ),
            'key' => array(
                'value' => $user->getKey(),
            ),
            'userId' => array(
                'value' => $userId,
            ),
        );
    }

}
