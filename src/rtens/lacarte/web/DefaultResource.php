<?php
namespace rtens\lacarte\web;

use rtens\lacarte\web\common\MenuResource;
use rtens\lacarte\WebResource;
use watoki\curir\http\Path;
use watoki\curir\http\Request;
use watoki\curir\http\Url;
use watoki\curir\resource\DynamicResource;
use watoki\curir\responder\Redirecter;

abstract class DefaultResource extends DynamicResource {

    /** @var \rtens\lacarte\core\Session <- */
    public $session;

    /** @var \rtens\lacarte\UserInteractor <- */
    public $userInteractor;

    /** @var \watoki\factory\Factory <- */
    public $factory;

    public function respond(Request $request) {
        if ($this->requiresLogin()) {
            if ($request->getParameters()->has('key')) {
                try {
                    $this->login($request->getParameters()->get('key'));
                } catch (\Exception $e) {
                    return $this->redirectToLogin($request);
                }
            } else if (!$this->isLoggedIn()) {
                return $this->redirectToLogin($request);
            }
        }

        return parent::respond($request);
    }

    protected function isLoggedIn() {
        return $this->isAdmin() || $this->isUser();
    }

    protected function isAdmin() {
        return $this->session->has('admin');
    }

    protected function isUser() {
        return $this->session->has('key');
    }

    protected function getAdminGroupId() {
        return $this->session->get('admin');
    }

    protected function getLoggedInUser() {
        return $this->userInteractor->readByKey($this->session->get('key'));
    }

    protected function login($key) {
        $user = $this->userInteractor->authorizeUser($key);

        if (!$user) {
            throw new \Exception('Invalid key');
        }

        $this->session->set('key', $user->getKey());
    }

    protected function assembleModel($model = array()) {
        $menu = $this->factory->getInstance(MenuResource::$CLASS, array(
            'url' => $this->getUrl(),
            'parent' => $this
        ));
        return array_merge(array(
            'menu' => $menu->doGet()->createResponse($menu, new Request(new Path()))->getBody(),
            'adminOnly' => $this->isAdmin(),
            'userOnly' => $this->isUser(),
        ), $model);
    }

    private function redirectToLogin(Request $request) {
        $redirecter = new Redirecter(Url::parse($this->getAncestor(WebResource::$CLASS)->getUrl('user/login.html')));
        return $redirecter->createResponse($this, $request);
    }

    protected function requiresLogin() {
        return true;
    }

}