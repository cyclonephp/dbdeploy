<?php

use cyclone\dbdeploy;

require_once realpath(__DIR__) . '/DBDeploy_Test.php';

/**
 * @author Bence Erős <crystal@cyclonephp.org>
 */
class DBDeploy_RevisionTest extends DBDeploy_Test {

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


    /**
     * @expectedException \cyclone\dbdeploy\Exception
     */
    public function test_constructor_failure() {
        $rev = new dbdeploy\Revision('commit
        -- //@UNDO
undo', 'ds', 10);
        $this->assertEquals('commit', $rev->commit);
        $this->assertEquals('undo', $rev->undo);

        $rev = new dbdeploy\Revision('commit
        -- //@UNDO
undo', 'ds', 10);
    }

    /**
     * @expectedException cyclone\dbdeploy\Exception
     */
    public function test_get_by_delta_set() {
        self::load_sample_revisions();
        $revisions = dbdeploy\Revision::get_by_delta_set('ds');
        $this->assertEquals(3, count($revisions), " 3 revisions loaded");
        $this->assertEquals(array(1, 2, 3), array_keys($revisions), "revisions are indexed properly");

        dbdeploy\Revision::get_by_delta_set('nonexistent');
    }

    /**
     * @expectedException cyclone\dbdeploy\Exception
     */
    public function test_get_by() {
        self::load_sample_revisions();
        $revision = dbdeploy\Revision::get_by('ds', 1);
        $this->assertEquals(1, $revision->revision_number);

        dbdeploy\Revision::get_by('ds', 20);
    }
}
