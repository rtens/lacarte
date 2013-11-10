<?php
namespace spec\rtens\lacarte\fixtures\resource\selections;

use rtens\lacarte\web\selections\xxSelectionResource;
use spec\rtens\lacarte\fixtures\resource\ResourceFixture;

/**
 * @property xxSelectionResource component
 */
class SelectionResourceFixture extends ResourceFixture {

    public function whenISetYieldedOfSelection_To($id, $value) {
        $this->responder = $this->component->doPut($id, $value);
    }

    public function thenTheErrorShouldContain($string) {
        $this->spec->assertContains($string, $this->getField('error'));
    }

    protected function getComponentClass() {
        return xxSelectionResource::$CLASS;
    }
}