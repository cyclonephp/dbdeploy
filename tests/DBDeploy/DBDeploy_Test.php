<?php

use cyclone\dbdeploy;
/**
 * @author Bence Erős <crystal@cyclonephp.org>
 */
class DBDeploy_Test extends Kohana_Unittest_TestCase {

    public function setUp() {
        parent::setUp();
        dbdeploy\Revision::clear_storage();
    }

    public static function load_sample_revisions() {
        new dbdeploy\Revision('commit1
-- //@UNDO
undo1', 'ds', 1);
        new dbdeploy\Revision('commit2
-- //@UNDO
undo2', 'ds', 2);
        new dbdeploy\Revision('commit3
-- //@UNDO
undo3', 'ds', 3);
    }

    protected function get_mock_storage() {
        return new dbdeploy\MockSourceReader(array(
            'ds' => array(
                new dbdeploy\Revision('commit1
-- //@UNDO
undo1', 'ds', 1),
        new dbdeploy\Revision('commit2
-- //@UNDO
undo2', 'ds', 2),
        new dbdeploy\Revision('commit3
-- //@UNDO
undo3', 'ds', 3)
            )
        ));
    }

}
