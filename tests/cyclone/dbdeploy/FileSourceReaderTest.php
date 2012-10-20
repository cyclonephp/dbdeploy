<?php
namespace cyclone\dbdeploy;

class FileSourceReaderTest extends DBDeployTest {

    public static function provider_parse_revision_filename() {
        return array(
            array('1-descr.sql', 1, 'descr', NULL),
            array('123-descr.sql', 123, 'descr', NULL),
            array('a-a', NULL, NULL, "invalid revision file name: 'a-a'"),
            array('111', NULL, NULL, "invalid revision file name: '111'"),
            array('1-a', 1, 'a', NULL),
            array('1-a-b', 1, 'a-b', NULL)
        );
    }

    /**
     * @dataProvider provider_parse_revision_filename
     */
    public function test_parse_revision_filename($filename, $rev, $descr, $exp_exception_msg) {
        try {
            list($actual_rev, $actual_descr) = FileSourceReader::parse_revision_filename($filename);
            $exp_exception_msg !== NULL && $this->fail("failed to throw exception");
        } catch (Exception $ex) {
            $this->assertEquals($exp_exception_msg, $ex->getMessage());
            return;
        }
        $this->assertEquals($rev, $actual_rev);
        $this->assertEquals($descr, $actual_descr);
    }

}