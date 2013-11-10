<?php
namespace spec\rtens\lacarte\specs\order;

use spec\rtens\lacarte\fixtures\resource\selections\SelectionResourceFixture;
use spec\rtens\lacarte\Specification;

/**
 * @property SelectionResourceFixture res <-
 */
class ChangeYieldedOfSelectionTest extends Specification {

    function testInvalidId() {
        $this->res->whenISetYieldedOfSelection_To(42, true);
        $this->res->thenTheErrorShouldContain('Selection with ID [42] not found');
    }

} 