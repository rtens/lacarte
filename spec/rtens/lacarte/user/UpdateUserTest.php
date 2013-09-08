<?php
namespace spec\rtens\lacarte\user;

use spec\rtens\lacarte\TestCase;

class UpdateUserTest extends TestCase {

    public function _testNoChange() {
        $this->given->theUser_WithTheEmail('Homer', 'donoughts@burns.com');

        $this->when->iUpdateTheUser();

        $this->then->thereShouldBeOneUser();
        $this->then->theUsersNameShouldBe('Homer');
        $this->then->theUsersEmailShouldBe('donoughts@burns.com');
    }

    public function _testChangeNameAndEmail() {
        $this->given->theUser_WithTheEmail('Homer', 'donoughts@burns.com');
        $this->given->iChangedTheNameTo('Bart');
        $this->given->iChangedTheEmailTo('eatmyshorts@burns.com');

        $this->when->iUpdateTheUser();

        $this->then->thereShouldBeOneUser();
        $this->then->theUsersNameShouldBe('Bart');
        $this->then->theUsersEmailShouldBe('eatmyshorts@burns.com');
    }


}