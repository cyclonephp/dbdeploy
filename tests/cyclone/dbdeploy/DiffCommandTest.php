<?php

namespace cyclone\dbdeploy;

require_once realpath(__DIR__) . '/DBDeployTest.php';

/**
 * @author Bence Erős <crystal@cyclonephp.org>
 */
class DiffCommandTest extends DBDeployTest {

    public function test_validation() {
        $fail_revs = array('asd', '1..k', 'k..1', 'k..k', '-1..0', '4..-1');
        foreach ($fail_revs as $rev) {
            $proc = CommandProcessor::factory('diff');
            $proc->setup(array(
                '--revision' => $rev
            ));
            try {
                $proc->get_result();
                $this->fail("failed to throw exception for revision '$rev'");
            } catch (Exception $ex) {}
        }
    }

    public function test_get_result_commit() {
        $proc = CommandProcessor::factory('diff');
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
        $proc = CommandProcessor::factory('diff');
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
