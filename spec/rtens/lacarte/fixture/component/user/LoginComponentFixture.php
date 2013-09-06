<?php
namespace spec\rtens\lacarte\fixture\component\user;

use rtens\lacarte\web\user\LoginComponent;

/**
 * @property LoginComponent $component
 * @property LoginComponent $component
 */
class LoginComponentFixture extends ComponentFixture {

    public static $CLASS = __CLASS__;

    public $email;

    public $password;

    public $key;

    public function givenIHaveEnteredTheAdminEmail($email) {
        $this->email = $email;
    }

    public function givenIHaveEnteredTheAdminPassword($password) {
        $this->password = $password;
    }

    public function whenILogInAsAdmin() {
        $this->model = $this->component->doLoginAdmin($this->email, $this->password);
    }

    public function thenTheErrorMessageShouldBe($msg) {
        $this->test->assertEquals($msg, $this->getField('error'));
    }

    public function thenTheAdminEmailFieldShouldContain($string) {
        $this->test->assertEquals($string, $this->getField('email'));
    }

    public function whenIOpenThePage() {
        $this->model = $this->component->doGet();
    }

    public function thenThereShouldBeNoErrorMessage() {
        $this->test->assertNull($this->getField('error'));
    }

    public function whenILogOut() {
        $this->model = $this->component->doLogout();
    }

    public function givenIHaveEnterTheKey($string) {
        $this->key = $string;
    }

    public function whenILogInAsUser() {
        $this->model = $this->component->doPost($this->key);
    }

    protected function getComponentClass() {
        return LoginComponent::$CLASS;
    }
}