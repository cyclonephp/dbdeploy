<?php

use cyclone\dbdeploy;

/**
 * @author Bence Erős <crystal@cyclonephp.org>
 */
class DBDeploy_RevisionTest extends Kohana_Unittest_TestCase {

    public function test_constructor() {
        $rev = new dbdeploy\Revision('', 'ds', 10);
        $this->assertEquals('ds', $rev->delta_set);
        $this->assertEquals(10, $rev->revision_number);
    }

    /**
     * @expectedException \cyclone\dbdeploy\Exception
     */
    public function test_extract_commit_undo() {
        $rev = new dbdeploy\Revision('commit
        -- //@UNDO
undo', 'ds', 10);
        $this->assertEquals('commit', $rev->commit);
        $this->assertEquals('undo', $rev->undo);

        $rev = new dbdeploy\Revision('commit
        -- //@UNDO
undo
-- //@UNDO', 'ds', 10);
    }

}
