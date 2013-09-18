<?php
namespace spec\rtens\lacarte\specs\user;

use spec\rtens\lacarte\fixtures\component\user\ListComponentFixture;
use spec\rtens\lacarte\fixtures\model\UserFixture;
use spec\rtens\lacarte\fixtures\service\FileFixture;
use spec\rtens\lacarte\fixtures\service\SessionFixture;
use spec\rtens\lacarte\Specification;

/**
 * @property SessionFixture session <-
 * @property UserFixture user <-
 * @property FileFixture files <-
 * @property ListComponentFixture component <-
 */
class UpdateUserTest extends Specification {

    protected function background() {
        $this->session->givenIAmLoggedInAsAdmin();
    }

    public function testNoChange() {
        $this->user->givenTheUser_WithTheEmail('Homer', 'homer@simpson.com');
        $this->component->givenIAmEditingTheUser('Homer');

        $this->component->whenISaveMyChanges();

        $this->component->thenThereShouldBeNoErrorMessage();
        $this->component->thenTheSuccessMessageShouldBe('The user has been updated');
        $this->user->thenThereShouldBe_Users(1);
        $this->user->thenThereShouldBeAUserWithTheName_TheEmail('Homer', 'homer@simpson.com');
    }

    public function testChangeNameAndEmail() {
        $this->user->givenTheUser_WithTheEmail('Homer', 'homer@simpson.com');
        $this->component->givenIAmEditingTheUser('Homer');

        $this->component->givenIHaveEnteredTheName('Bart');
        $this->component->givenIHaveEnteredTheEmail('EatMyShorts@burns.com');

        $this->component->whenISaveMyChanges();

        $this->component->thenThereShouldBeNoErrorMessage();
        $this->component->thenTheSuccessMessageShouldBe('The user has been updated');
        $this->user->thenThereShouldBe_Users(1);
        $this->user->thenThereShouldBeAUserWithTheName_TheEmail('Bart', 'eatmyshorts@burns.com');
    }

    public function testMissingData() {
        $this->user->givenTheUser_WithTheEmail('Homer', 'homer@simpson.com');
        $this->component->givenIAmEditingTheUser('Homer');

        $this->component->givenIHaveEnteredTheName('');
        $this->component->givenIHaveEnteredTheEmail('EatMyShorts@burns.com');

        $this->component->whenISaveMyChanges();

        $this->component->thenThereShouldBeNoSuccessMessage();
        $this->component->thenTheErrorMessageShouldBe('Could not update user. Missing data.');
        $this->component->thenIShouldStillBeEditingTheUser('Homer');
        $this->component->thenTheEditingNameFieldShouldContain('');
        $this->component->thenTheEditingEmailFieldShouldContain('EatMyShorts@burns.com');
    }

    public function testChangeAvatar() {
        $this->user->givenTheUser_WithTheEmail('Homer', 'homer@simpson.com');
        $this->component->givenIAmEditingTheUser('Homer');
        $this->component->givenIHaveSelectedAnAvatarFile('avatar.JPG');

        $this->component->whenISaveMyChanges();

        $this->component->thenThereShouldBeNoErrorMessage();
        $this->component->thenTheSuccessMessageShouldBe('The user has been updated');
        $this->user->thenThereShouldBe_Users(1);
        $this->files->then_ShouldHaveAnAvatar('Homer');
    }

    public function testWrongPictureFormat() {
        $this->user->givenTheUser_WithTheEmail('Homer', 'homer@simpson.com');
        $this->component->givenIAmEditingTheUser('Homer');
        $this->component->givenIHaveSelectedAnAvatarFile('avatar.png');

        $this->component->givenIHaveEnteredTheEmail('Some@mail.com');
        $this->component->givenIHaveEnteredTheName('Some Name');

        $this->component->whenISaveMyChanges();

        $this->component->thenTheErrorMessageShouldBe('Only jpg-files allowed.');
        $this->component->thenIShouldStillBeEditingTheUser('Homer');
        $this->component->thenTheEditingNameFieldShouldContain('Some Name');
        $this->component->thenTheEditingEmailFieldShouldContain('Some@mail.com');
    }

}