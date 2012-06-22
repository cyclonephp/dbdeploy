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

}
