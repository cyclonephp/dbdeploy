<?php

namespace cyclone\dbdeploy;

use cyclone as cy;

/**
 * @package dbdeploy
 * @author Bence Erős <crystal@cyclonephp.org>
 */
class FileSourceReader implements SourceReader{

    /**
     * The absolute path of the source reader.
     *
     * @var string
     */
    protected $_src_dir;

    /**
     * The number of revisions per-deltaset.
     *
     * @var array
     */
    private $_revision_count = array();

    public function __construct($src_dir) {
        $this->_src_dir = $src_dir;
    }

    /**
     * @param $delta_set string the name of the delta set
     * @param $revision_number int the revision number
     * @return Revision
     */
    public function get_revision_source($delta_set, $revision_number) {
        $dir = $this->get_delta_set_dir_name($delta_set);
        $pattern = $dir . $revision_number . '*';
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

        list(, $descr) = self::parse_revision_filename($file);

        return new Revision(file_get_contents($file), $delta_set, $revision_number, $descr);
    }

    public function get_delta_set_dir_name($delta_set) {
        if ($delta_set == '') {
            return $this->_src_dir . \DIRECTORY_SEPARATOR;
        } else {
            return $this->_src_dir  . \DIRECTORY_SEPARATOR . $delta_set . \DIRECTORY_SEPARATOR;
        }
    }

    public function load_revisions($delta_set) {
        $dir = $this->get_delta_set_dir_name($delta_set);

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

            list($curr_rev_number, $descr) = static::parse_revision_filename($file_name);

            if ($curr_rev_number !== $last_rev_number + 1 && $curr_rev_number > 1)
                throw new Exception("revision numbers are not sequential: '$last_file_name' is followed by '$file_name'");

            if ( ! is_readable($full_file_name))
                throw new Exception("$full_file_name is not readable");

            $rval [$curr_rev_number]= new Revision(file_get_contents($full_file_name)
                , $delta_set, $curr_rev_number, $descr);

            $last_file_name = $file_name;
            ++$last_rev_number;
        }
        $this->_revision_count[$delta_set] = $last_rev_number;
        return $rval;
    }

    public static function parse_revision_filename($filename) {
        $dash_parts = explode('-', $filename);
        if ( ! is_numeric($dash_parts[0]) || count($dash_parts) < 2)
            throw new Exception("invalid revision file name: '$filename'");

        $rev_num = (int) array_shift($dash_parts);

        $descr = implode('-', $dash_parts);
        $last_dot_pos = strrpos($descr, '.');

        if ($last_dot_pos !== FALSE) {
            $descr = substr($descr, 0, $last_dot_pos);
        }

        return array($rev_num, $descr);
    }

    /**
     * Returns the latest (highest number) revision available in the data source.
     * @param $delta_set
     * @return int
     */
    public function latest_revision($delta_set) {
        if ( ! isset($this->_revision_count[$delta_set]))
            throw new Exception("delta set '$delta_set' is not loaded");

        return $this->_revision_count[$delta_set];
    }


}
