<?php

namespace cyclone\dbdeploy;

use cyclone as cy;

/**
 * @author Bence Erős <crystal@cyclonephp.org>
 */
class FileSourceReader implements SourceReader{

    /**
     * The absolute path of the source reader.
     *
     * @var string
     */
    protected $_src_dir;

    public function __construct($src_dir) {
        $this->_src_dir = $src_dir;
    }

    /**
     * @param $delta_set string the name of the delta set
     * @param $revision_number int the revision number
     * @return Revision
     */
    public function get_revision_source($delta_set, $revision_number) {
        if ($delta_set == '') {
            $pattern = $this->_src_dir . '/' . $revision_number . '*';
        } else {
            $pattern = $this->_src_dir . '/' . $delta_set . '/' . $revision_number . '*';
        }
        $files = glob($pattern);
        switch(count($files)) {
            case 0:
                throw new Exception("no files matched pattern '$pattern'");
            case 1:
                $file = $files[0];
                break;
            default:
                throw new Exception(count($files) . " files matched pattern '$pattern' (expected exactly 1)");
        }
        if ( ! is_readable($file))
            throw new Exception("$file is not readable");

        return new Revision(file_get_contents($file), $delta_set, $revision_number);
    }


}
