<?php
namespace cyclone\dbdeploy;

/**
 * @author Bence Erős <crystal@cyclonephp.org>
 */
class MockSourceReader implements SourceReader {

    /**
     * @var array
     */
    protected $_storage;

    public function __construct($storage) {
        $this->_storage = $storage;
    }

    /**
     * @param $delta_set string the name of the delta set
     * @param $revision_number int the revision number
     * @return Revision
     */
    public function get_revision_source($delta_set, $revision_number) {
        return $this->_storage[$delta_set][$revision_number];
    }


}
