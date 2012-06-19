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

}
