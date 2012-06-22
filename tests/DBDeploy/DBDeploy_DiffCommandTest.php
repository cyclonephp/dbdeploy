<?php

use cyclone\dbdeploy;

require_once realpath(__DIR__) . '/DBDeploy_Test.php';

/**
 * @author Bence Erős <crystal@cyclonephp.org>
 */
class DBDeploy_DiffCommandTest extends DBDeploy_Test {

    public function test_get_result_commit() {
        $proc = dbdeploy\CommandProcessor::factory('diff');
        $proc->setup(array(
            '--revision' => '1..3',
            '--delta-set' => 'ds'
        ));
        $proc->set_source_reader($this->get_mock_storage());
        $this->assertEquals('commit2
commit3
', $proc->get_result());
    }

    public function test_get_result_undo() {
        $proc = dbdeploy\CommandProcessor::factory('diff');
        $proc->setup(array(
            '--revision' => '3..1',
            '--delta-set' => 'ds'
        ));
        $proc->set_source_reader($this->get_mock_storage());
        $this->assertEquals('undo3
undo2
', $proc->get_result());
    }
}
