<?php
namespace spec\rtens\lacarte\fixtures\component\user;

use rtens\lacarte\web\user\LoginResource;
use spec\rtens\lacarte\fixtures\component\ResourceFixture;

/**
 * @property LoginResource component
 */
class LoginComponentFixture extends ResourceFixture {

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
        $this->responder = $this->component->doLoginAdmin($this->email, $this->password);
    }

    public function thenTheErrorMessageShouldBe($msg) {
        $this->spec->assertEquals($msg, $this->getField('error'));
    }

    public function thenTheAdminEmailFieldShouldContain($string) {
        $this->spec->assertEquals($string, $this->getField('email/value'));
    }

    public function whenIOpenThePage() {
        $this->responder = $this->component->doGet();
    }

    public function thenThereShouldBeNoErrorMessage() {
        $this->spec->assertNull($this->getField('error'));
    }

    public function whenILogOut() {
        $this->responder = $this->component->doLogout();
    }

    public function givenIHaveEnterTheKey($string) {
        $this->key = $string;
    }

    public function whenILogInAsUser() {
        $this->responder = $this->component->doPost($this->key);
    }

    protected function getComponentClass() {
        return LoginResource::$CLASS;
    }
}