<?php

namespace cyclone\dbdeploy;

/**
 * Abstract source reader which can read the delta source written by an application developer.
 *
 * @author Bence Erős <crystal@cyclonephp.org>
 * @package dbdeploy
 */
interface SourceReader {

    /**
     * @param $delta_set string the name of the delta set
     * @param $revision_number int the revision number
     * @return Revision
     */
    public function get_revision_source($delta_set, $revision_number);

    /**
     * Loads the revisions from the data source managed by the implementation.
     *
     * @param $delta_set the delta set to be loaded
     */
    public function load_revisions($delta_set);

    /**
     * Returns the latest (highest number) revision available in the data source.
     * @param $delta_set
     * @return int
     */
    public function latest_revision($delta_set);

}
