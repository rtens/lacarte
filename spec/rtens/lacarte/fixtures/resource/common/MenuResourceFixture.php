<?php
namespace spec\rtens\lacarte\fixtures\resource\common;

use rtens\lacarte\web\common\MenuResource;
use spec\rtens\lacarte\fixtures\resource\ResourceFixture;
use watoki\curir\http\Path;
use watoki\curir\http\Request;
use watoki\dom\Element;
use watoki\dom\Parser;

/**
 * @property MenuResource component
 */
class MenuResourceFixture extends ResourceFixture {

    /** @var Parser */
    private $dom;

    public function whenITheMenuIsRendered() {
        $request = new Request(new Path(), array('html'));
        $response = $this->component->doGet()->createResponse($request);
        $this->dom = new Parser($response->getBody());
    }

    public function thenTheOrdersLinkShouldGoTo($string) {
        /** @var Element $element */
        $element = $this->getLeftMenuElement()->getChildElements('a')->get(0);
        $this->spec->assertEquals($string, $element->getAttribute('href')->getValue());
    }

    public function thenTheTodayLinkShouldGoTo($string) {
        /** @var Element $element */
        $element = $this->getLeftMenuElement()->getChildElements('a')->get(1);
        $this->spec->assertEquals($string, $element->getAttribute('href')->getValue());
    }

    public function thenTheLogoutLinkShouldGoTo($string) {
        /** @var Element $element */
        $element = $this->getRightMenuElement()->getChildElements('a')->get(0);
        $this->spec->assertEquals($string, $element->getAttribute('href')->getValue());
    }

    public function thenTheGithubLogoShouldHaveTheSource($string) {
        /** @var Element $link */
        $link = $this->getRightMenuElement()->getChildElements('a')->get(1);
        /** @var Element $image */
        $image = $link->findChildElement('img');
        $this->spec->assertEquals($string, $image->getAttribute('src')->getValue());
    }

    protected function getComponentClass() {
        return MenuResource::$CLASS;
    }

    /**
     * @return null|Element
     */
    private function getBodyElement() {
        return $this->dom->getRoot()->findChildElement('html')->findChildElement('body');
    }

    /**
     * @return mixed
     */
    private function getLeftMenuElement() {
        return $this->getBodyElement()->getChildElements('div')->get(0);
    }

    /**
     * @return mixed
     */
    private function getRightMenuElement() {
        return $this->getBodyElement()->getChildElements('div')->get(1);
    }
}