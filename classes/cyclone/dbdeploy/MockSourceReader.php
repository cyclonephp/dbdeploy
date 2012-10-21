<?php
namespace cyclone\dbdeploy;

/**
 * @author Bence Erős <crystal@cyclonephp.org>
 * @package dbdeploy
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

    public function load_revisions($delta_set) {
        if ( ! isset($this->_storage[$delta_set]))
            throw new Exception("delta set '$delta_set' not found");

        return $this->_storage[$delta_set];
    }

    /**
     * Returns the latest (highest number) revision available in the data source.
     * @param $delta_set
     * @return int
     */
    public function latest_revision($delta_set) {
        return count($this->_storage[$delta_set]);
    }


}
