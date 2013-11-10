<?php
namespace rtens\lacarte\model\migration;

class Step2 extends BaseStep {

    public function up() {
        $this->db->execute('ALTER TABLE selections
                            ADD COLUMN yielded INTEGER DEFAULT 0');
    }

    public function down() {
    }
}