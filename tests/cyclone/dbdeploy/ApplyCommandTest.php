<?php
namespace cyclone\dbdeploy;

require __DIR__ . DIRECTORY_SEPARATOR . 'DBDeployTest.php';

class ApplyCommandTest extends DBDeployTest {

    public function test_apply() {
        $this->load_sample_revisions();
        $proc = CommandProcessor::factory('apply');
        $proc->setup(array(
            '--revision' => 1,
            '--delta-set' => 'ds'
        ));
    }

}