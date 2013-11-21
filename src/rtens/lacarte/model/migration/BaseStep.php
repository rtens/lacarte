<?php
namespace rtens\lacarte\model\migration;

use rtens\lacarte\core\Database;
use watoki\factory\Factory;
use watoki\stepper\Step;

abstract class BaseStep implements Step {

    /** @var Database */
    protected $db;

    /** @var \watoki\factory\Factory */
    protected $factory;

    function __construct(Database $db, Factory $factory) {
        $this->db = $db;
        $this->factory = $factory;
    }

}