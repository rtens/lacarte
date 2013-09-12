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
                'error' => 'Access denied. Must be administrator.',
                'email' => array('value' => $email),
                'name' => array('value' => $name)
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

    public function doSave($name, $email, $userId) {
        if (!$this->isAdmin()) {
            return $this->redirect(Url::parse('../order/list.html'));
        }

        if (isset($_FILES['picture']) && $_FILES['picture']['name']) {
            $picture = $_FILES['picture']['name'];
            $pictureTmp = $_FILES['picture']['tmp_name'];

            if ('jpg' != strtolower(substr($picture, strrpos($picture, '.') + 1))) {
                return $this->assembleModel(array(
                    'error' => 'Only jpg-files allowed.',
                    'notEditing' => null,
                    'editing' => $this->assembleEditingUser($userId, $name, $email)
                ));
            };

            $avatarDir = $this->files->getUserFilesDirectory() . '/avatars';
            $avatarPath = $avatarDir . '/' . $userId . '.jpg';

            @mkdir($avatarDir, 0777, true);
            if(!$this->files->moveUploadedFile($pictureTmp, $avatarPath)) {
                return $this->assembleModel(array(
                    'error' => 'There was an error uploading the file, please try again!'
                ));
            }
        }

        $user = $this->userInteractor->readById($userId);
        if(!$email || !$name) {
            return $this->assembleModel(array(
                'error' => 'Could not update user. Missing data.',
                'notEditing' => null,
                'editing' => $this->assembleEditingUser($userId, $name, $email)
            ), $userId);
        }
        $user->setEmail($email);
        $user->setName($name);
        try {
            $this->userInteractor->updateUser($user);
            return $this->assembleModel(array(
                'success' => 'The user has been updated'
            ));
        } catch (\Exception $e) {
            return $this->assembleModel(array(
                'error' => $e->getMessage()
            ));
        }
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
        return $this->assembleEditingUser($userId, $user->getName(), $user->getEmail());
    }

    private function assembleEditingUser($userId, $name, $email) {
        return array(
            'name' => array(
                'value' => $name
            ),
            'email' => array(
                'value' => $email,
            ),
            'id' => array(
                'value' => $userId,
            ),
        );
    }

}
