<?php
namespace rtens\lacarte\model\migration;

use rtens\lacarte\core\Database;
use watoki\stepper\Step;

abstract class BaseStep implements Step {

    /**
     * @var Database
     */
    protected $db;

    function __construct(Database $db) {
        $this->db = $db;
    }

}