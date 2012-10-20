<?php
namespace cyclone\dbdeploy;

use cyclone\DB;

class ChangelogManagerTest extends DBDeployTest {

    const CHANGELOG_TABLE = 'changelog_table';

    const CONN = 'cytst-dbdeploy';

    const DS = 'ds';

    const DUMMY_TBL = "blahh";

    /**
     * @var \cyclone\dbdeploy\ChangelogManager
     */
    private $_mgr;

    public function setUp() {
        parent::setUp();
        $this->_mgr = new ChangelogManager(self::CONN, self::CHANGELOG_TABLE, self::DS);
    }

    public function tearDown() {
        DB::query('DROP TABLE IF EXISTS ' . self::DUMMY_TBL)->exec(self::CONN);
        DB::query('DROP TABLE IF EXISTS ' . self::CHANGELOG_TABLE)->exec(self::CONN);
        parent::tearDown();
    }

    public function test_apply() {
        $rev = new Revision("create table " . self::DUMMY_TBL . "(id int);
-- //@UNDO
drop table ". self::DUMMY_TBL . ";", self::DS, 1);
        $this->_mgr->apply($rev);
        $result = DB::select()->from(self::CHANGELOG_TABLE)
            ->exec(self::CONN)->as_array();
        $this->assertEquals(1, count($result));
        $result = $result[0];
        $this->assertEquals('ds', $result['delta_set']);
        $this->assertEquals('1', $result['change_number']);
        $this->assertEquals(ChangelogManager::$applied_by, $result['applied_by']);
    }

    public function test_undo() {
        $rev = new Revision("create table " . self::DUMMY_TBL . "(id int);
-- //@UNDO
drop table ". self::DUMMY_TBL . ";", self::DS, 1);
        $this->_mgr->apply($rev);

        $rev = new Revision("insert into " . self::DUMMY_TBL . " values(10);
-- //@UNDO
delete from ". self::DUMMY_TBL . " where id = 10;", self::DS, 2);
        $this->_mgr->apply($rev);

        $this->_mgr->undo($rev);

        $result = DB::select()->from(self::CHANGELOG_TABLE)
            ->exec(self::CONN)->as_array();
        $this->assertEquals(1, count($result));
        $result = $result[0];
        $this->assertEquals('ds', $result['delta_set']);
        $this->assertEquals('1', $result['change_number']);

        $result = DB::select()->from(self::DUMMY_TBL)->exec(self::CONN)->as_array();
        $this->assertEquals(0, count($result));
    }

    public function test_current() {
        $rev = new Revision("create table " . self::DUMMY_TBL . "(id int);
-- //@UNDO
drop table ". self::DUMMY_TBL . ";", self::DS, 1);
        $this->_mgr->apply($rev);

        $this->assertEquals(1, $this->_mgr->current(self::DS));

        $rev = new Revision("insert into " . self::DUMMY_TBL . " values(10);
-- //@UNDO
delete from ". self::DUMMY_TBL . " where id = 10;", self::DS, 2);
        $this->_mgr->apply($rev);

        $this->assertEquals(2, $this->_mgr->current(self::DS));


    }
}