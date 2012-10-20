<?php

namespace cyclone\dbdeploy;

require_once realpath(__DIR__) . '/DBDeployTest.php';

/**
 * @author Bence Erős <crystal@cyclonephp.org>
 */
class RevisionTest extends DBDeployTest {

    public function test_constructor() {
        $rev = new Revision('', 'ds', 10, 'descr');
        $this->assertEquals('ds', $rev->delta_set);
        $this->assertEquals(10, $rev->revision_number);
        $this->assertEquals('descr', $rev->description);
    }

    /**
     * @expectedException \cyclone\dbdeploy\Exception
     */
    public function test_extract_commit_undo() {
        $rev = new Revision('commit
        -- //@UNDO
undo', 'ds', 10, 'descr');
        $this->assertEquals('commit', $rev->commit);
        $this->assertEquals('undo', $rev->undo);

        $rev = new Revision('commit
        -- //@UNDO
undo
-- //@UNDO', 'ds', 10, 'descr');
    }


    /**
     * @expectedException \cyclone\dbdeploy\Exception
     */
    public function test_constructor_failure() {
        $rev = new Revision('commit
        -- //@UNDO
undo', 'ds', 10, 'descr');
        $this->assertEquals('commit', $rev->commit);
        $this->assertEquals('undo', $rev->undo);

        $rev = new Revision('commit
        -- //@UNDO
undo', 'ds', 10, 'descr');
    }

    /**
     * @expectedException cyclone\dbdeploy\Exception
     */
    public function test_get_by_delta_set() {
        self::load_sample_revisions();
        $revisions = Revision::get_by_delta_set('ds');
        $this->assertEquals(3, count($revisions), " 3 revisions loaded");
        $this->assertEquals(array(1, 2, 3), array_keys($revisions), "revisions are indexed properly");

        Revision::get_by_delta_set('nonexistent');
    }

    /**
     * @expectedException cyclone\dbdeploy\Exception
     */
    public function test_get_by() {
        self::load_sample_revisions();
        $revision = Revision::get_by('ds', 1);
        $this->assertEquals(1, $revision->revision_number);

        Revision::get_by('ds', 20);
    }
}
