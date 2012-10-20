<?php

namespace cyclone\dbdeploy;
/**
 * @author Bence Erős <crystal@cyclonephp.org>
 */
class DBDeployTest extends \Kohana_Unittest_TestCase {

    public function tearDown() {
        Revision::clear_storage();
    }

    public static function load_sample_revisions() {
        new Revision('commit1
-- //@UNDO
undo1', 'ds', 1, 'descr');
        new Revision('commit2
-- //@UNDO
undo2', 'ds', 2, 'descr');
        new Revision('commit3
-- //@UNDO
undo3', 'ds', 3, 'descr');
    }

    protected function get_mock_storage() {
        return new MockSourceReader(array(
            'ds' => array(
                new Revision('commit1
-- //@UNDO
undo1', 'ds', 1, 'descr'),
        new Revision('commit2
-- //@UNDO
undo2', 'ds', 2, 'descr'),
        new Revision('commit3
-- //@UNDO
undo3', 'ds', 3, 'descr')
            )
        ));
    }

}
