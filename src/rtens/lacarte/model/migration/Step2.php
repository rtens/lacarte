<?php
namespace rtens\lacarte\model\migration;

use watoki\stepper\Step;

class Step2 extends BaseStep {

    public static $CLASS = __CLASS__;

    public function up() {
        $this->db->execute('ALTER TABLE selections
                            ADD COLUMN yielded INTEGER DEFAULT 0');
    }

    public function down() {
    }

    /**
     * @return Step|null Return the next step or null if this was the last step
     */
    public function next() {
        return null;
    }

    /**
     * @return boolean
     */
    public function canBeUndone() {
        return true;
    }
}