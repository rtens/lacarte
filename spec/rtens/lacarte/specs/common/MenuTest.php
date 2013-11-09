<?php
namespace spec\rtens\lacarte\specs\order;

use spec\rtens\lacarte\fixtures\resource\common\MenuResourceFixture;
use spec\rtens\lacarte\fixtures\service\SessionFixture;
use spec\rtens\lacarte\Specification;

/**
 * @property SessionFixture session <-
 * @property MenuResourceFixture res <-
 */
class MenuTest extends Specification {

    function testUserMenuEntries() {
        $this->session->givenIAmLoggedAsTheUser('Bart');
        $this->res->whenITheMenuIsRendered();

        $this->res->thenTheOrdersLinkShouldGoTo('http://lacarte/common/../order/list.html');
        $this->res->thenTheTodayLinkShouldGoTo('http://lacarte/common/../order/todaysDishes.html');
        $this->res->thenTheLogoutLinkShouldGoTo('http://lacarte/common/../user/login.html?method=logout');
        $this->res->thenTheGithubLogoShouldHaveTheSource('http://lacarte/common/github.png');
    }

} 