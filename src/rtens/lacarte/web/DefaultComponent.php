<?php
namespace rtens\lacarte\web;

use rtens\lacarte\UserInteractor;
use rtens\lacarte\core\Session;
use rtens\lacarte\web\common\MenuComponent;
use watoki\curir\Path;
use watoki\curir\Request;
use watoki\curir\Url;
use watoki\curir\composition\SuperComponent;
use watoki\curir\controller\Module;
use watoki\curir\renderer\RendererFactory;
use watoki\factory\Factory;
use watoki\tempan\Renderer;

abstract class DefaultComponent extends SuperComponent {

    /**
     * @var \rtens\lacarte\core\Session
     */
    protected $session;

    protected $userInteractor;

    function __construct(Factory $factory, Path $route, Module $parent = null,
        UserInteractor $userInteractor, Session $session) {

        parent::__construct($factory, $route, $parent);
        $this->userInteractor = $userInteractor;
        $this->session = $session;

        $this->rendererFactory->setRenderer('html', Renderer::$CLASS);
    }

    public function respond(Request $request) {
        if ($this->requiresLogin() && !$this->isLoggedIn()) {
            if ($request->getParameters()->has('key')) {
                try {
                    $this->login($request->getParameters()->get('key'));
                } catch (\Exception $e) {
                    return $this->redirectToLogin();
                }
            } else {
                return $this->redirectToLogin();
            }
        }

        return parent::respond($request);
    }

    protected function isLoggedIn() {
        return $this->isAdmin() || $this->session->hasAndGet('key');
    }

    protected function isAdmin() {
        return $this->session->has('admin');
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
        return array_merge(array(
            'menu' => $this->subComponent(MenuComponent::$CLASS),
            'adminOnly' => $this->isAdmin(),
            'userOnly' => $this->isLoggedIn() && !$this->isAdmin(),
        ), $model);
    }

    /**
     * @return \watoki\curir\Response
     */
    protected function redirectToLogin() {
        $this->redirect(Url::parse($this->getRoot()->getRoute()->toString() . '/user/login.html'));
        return $this->getResponse();
    }

    protected function requiresLogin() {
        return true;
    }

}