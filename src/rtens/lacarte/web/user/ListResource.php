<?php
namespace rtens\lacarte\web\user;
 
use rtens\lacarte\model\User;
use rtens\lacarte\Presenter;
use rtens\lacarte\web\DefaultResource;
use watoki\curir\http\Url;
use watoki\curir\responder\Redirecter;

class ListResource extends DefaultResource {

    public static $CLASS = __CLASS__;

    /** @var \rtens\lacarte\core\FileRepository <- */
    private $files;

    public function doGet() {
        if (!$this->isAdmin()) {
            return new Redirecter(Url::parse('../order/list.html'));
        }
        return new Presenter($this->assembleModel());
    }

    public function doEdit($user) {
        if (!$this->isAdmin()) {
            return new Redirecter(Url::parse('../order/list.html'));
        }
        return new Presenter($this->assembleModel(array(), $user));
    }

    public function doPost($name, $email) {
        if (!$this->isLoggedIn()) {
            return new Redirecter(Url::parse('login.html'));
        }

        if (!$this->isAdmin()) {
            return new Presenter($this->assembleModel(array(
                'error' => 'Access denied. Must be administrator.',
                'email' => array('value' => $email),
                'name' => array('value' => $name)
            )));
        }

        try {
            $groupId = $this->session->get('admin');
            $this->userInteractor->createUser($groupId, $name, $email);

            return new Presenter($this->assembleModel(array(
                'success' => "The user $name was created."
            )));
        } catch (\Exception $e) {
            return new Presenter($this->assembleModel(array(
                'error' => $e->getMessage(),
                'email' => array('value' => $email),
                'name' => array('value' => $name)
            )));
        }
    }

    public function doDelete($user) {
        if (!$this->isAdmin()) {
            return new Redirecter(Url::parse('../order/list.html'));
        }

        $entity = new User(1, '', '', '');
        $entity->id = $user;
        $this->userInteractor->delete($entity);

        return new Presenter($this->assembleModel(array(
            'success' => 'The user has been deleted'
        )));
    }

    public function doSave($name, $email, $userId) {
        if (!$this->isAdmin()) {
            return new Redirecter(Url::parse('../order/list.html'));
        }

        if (isset($_FILES['picture']) && $_FILES['picture']['name']) {
            $picture = $_FILES['picture']['name'];
            $pictureTmp = $_FILES['picture']['tmp_name'];

            if ('jpg' != strtolower(substr($picture, strrpos($picture, '.') + 1))) {
                return new Presenter($this->assembleModel(array(
                    'error' => 'Only jpg-files allowed.',
                    'notEditing' => null,
                    'editing' => $this->assembleEditingUser($userId, $name, $email)
                )));
            };

            $avatarDir = $this->files->getUserFilesDirectory() . '/avatars';
            $avatarPath = $avatarDir . '/' . $userId . '.jpg';

            @mkdir($avatarDir, 0777, true);
            if(!$this->files->moveUploadedFile($pictureTmp, $avatarPath)) {
                return new Presenter($this->assembleModel(array(
                    'error' => 'There was an error uploading the file, please try again!'
                )));
            }
        }

        $user = $this->userInteractor->readById($userId);
        if(!$email || !$name) {
            return new Presenter($this->assembleModel(array(
                'error' => 'Could not update user. Missing data.',
                'notEditing' => null,
                'editing' => $this->assembleEditingUser($userId, $name, $email)
            ), $userId));
        }
        $user->setEmail($email);
        $user->setName($name);
        try {
            $this->userInteractor->updateUser($user);
            return new Presenter($this->assembleModel(array(
                'success' => 'The user has been updated'
            )));
        } catch (\Exception $e) {
            return new Presenter($this->assembleModel(array(
                'error' => $e->getMessage()
            )));
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
