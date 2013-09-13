<?php
namespace spec\rtens\lacarte\fixtures\component\user;

use rtens\lacarte\web\user\ListComponent;
use rtens\mockster\MockFactory;
use spec\rtens\lacarte\fixtures\component\ComponentFixture;
use spec\rtens\lacarte\fixtures\model\UserFixture;
use spec\rtens\lacarte\fixtures\service\FileFixture;

/**
 * @property ListComponent component
 * @property UserFixture user<-
 * @property FileFixture files<-
 */
class ListComponentFixture extends ComponentFixture {

    public static $CLASS = __CLASS__;

    private $newName;

    private $newEmail;

    public function givenIHaveEnteredTheName($string) {
        $this->newName = $string;
    }

    public function givenIHaveEnteredTheEmail($string) {
        $this->newEmail = $string;
    }

    public function whenICreateANewUser() {
        $this->model = $this->component->doPost($this->newName, $this->newEmail);
    }

    public function whenIAccessTheUserList() {
        $this->model = $this->component->doGet();
    }

    public function whenIDeleteTheUser($name) {
        $this->model = $this->component->doDelete($this->user->getUser($name)->id);
    }

    public function thenTheSuccessMessageShouldBe($string) {
        $this->spec->assertEquals($string, $this->getField('success'));
    }

    public function thenTheErrorMessageShouldBe($string) {
        $this->spec->assertEquals($string, $this->getField('error'));
    }

    public function thenTheNewNameFieldShouldContain($string) {
        $this->spec->assertEquals($string, $this->getField('name/value'));
    }

    public function thenTheEmailFieldShouldContain($string) {
        $this->spec->assertEquals($string, $this->getField('email/value'));
    }

    public function thenTheUserListShouldBeEmpty() {
        $this->spec->assertCount(0, $this->getField('user'));
    }

    public function thenThereShouldBe_Users($count) {
        $this->spec->assertCount($count, $this->getField('user'));
    }

    public function thenTheAvatarOfUserAtPosition_ShouldBe($position, $imgSrc) {
        $i = $position - 1;
        $this->spec->assertEquals($imgSrc, $this->getField("user/$i/avatar/src"));
    }

    public function givenIAmEditingTheUser($userName) {
        $this->model = $this->component->doEdit($this->user->getUser($userName)->id);
        $this->newEmail = $this->getField('editing/email/value');
        $this->newName = $this->getField('editing/name/value');

        $_FILES['picture'] = array(
            'name' => '',
            'tmp_name' => ''
        );
    }

    public function whenISaveMyChanges() {
        $this->model = $this->component->doSave($this->newName, $this->newEmail, $this->getField('editing/id/value'));
    }

    public function thenThereShouldBeNoSuccessMessage() {
        $this->thenTheSuccessMessageShouldBe(null);
    }

    public function thenThereShouldBeNoErrorMessage() {
        $this->thenTheErrorMessageShouldBe(null);
    }

    public function givenIHaveSelectedAnAvatarFile($fileName) {
        $this->files->givenTheFile($fileName);

        $_FILES['picture']['name'] = $fileName;
        $_FILES['picture']['tmp_name'] = $this->files->getFullPath($fileName);
    }

    public function thenIShouldStillBeEditingTheUser($userName) {
        $this->spec->assertNotNull($this->getField('editing'));
        $this->spec->assertEquals($this->user->getUser($userName)->id, $this->getField('editing/id/value'));
    }

    public function thenTheEditingNameFieldShouldContain($string) {
        $this->spec->assertEquals($string, $this->getField('editing/name/value'));
    }

    public function thenTheEditingEmailFieldShouldContain($string) {
        $this->spec->assertEquals($string, $this->getField('editing/email/value'));
    }

    protected function getComponentClass() {
        return ListComponent::$CLASS;
    }
}