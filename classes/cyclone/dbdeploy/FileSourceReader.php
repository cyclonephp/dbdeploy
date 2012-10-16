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
            $pattern = $this->_src_dir . \DIRECTORY_SEPARATOR . $revision_number . '*';
        } else {
            $pattern = $this->_src_dir . \DIRECTORY_SEPARATOR . $delta_set . \DIRECTORY_SEPARATOR . $revision_number . '*';
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

    public function load_revisions($delta_set) {
        if ($delta_set == '') {
            $dir = $this->_src_dir . \DIRECTORY_SEPARATOR;
        } else {
            $dir = $this->_src_dir  . \DIRECTORY_SEPARATOR . $delta_set . \DIRECTORY_SEPARATOR;
        }

        if ( ! is_dir($dir))
            throw new Exception("$dir is not a directory");

        $rval = array();
        $files = glob($dir . '*');
        if (count($files) === 0)
            throw new Exception("directory $dir is empty");

        $dir_len = strlen($dir);
        $last_rev_number = 0;
        $last_file_name = '';
        foreach ($files as $full_file_name) {
            if (is_dir($full_file_name))
                continue;

            $file_name = substr($full_file_name, $dir_len);
            if ( ! is_numeric($file_name{0}))
                throw new Exception("invalid revision file name: $file_name");

            // converts the file name to int - which means picking its number prefix
            $curr_rev_number = (int) $file_name;
            if ($curr_rev_number !== $last_rev_number + 1)
                throw new Exception("revision numbers are not sequential: '$last_file_name' is followed by '$file_name'");

            if ( ! is_readable($full_file_name))
                throw new Exception("$full_file_name is not readable");

            $rval [$curr_rev_number]= new Revision(file_get_contents($full_file_name), $delta_set, $curr_rev_number);

            $last_file_name = $file_name;
            ++$last_rev_number;
        }
        return $rval;
    }


}
