<?php

namespace cyclone\dbdeploy;

/**
 * Model class representing revisions (deltas).
 *
 * @author Bence Erős <crystal@cyclonephp.org>
 * @package dbdeploy
 * @property-read $commit string
 * @property-read $undo $string
 */
class Revision {

    /**
     * The "commit" part of the source file (before -- //UNDO)
     *
     * @var string
     */
    protected $_commit;

    /**
     * The "undo" part of the source file (after -- //UNDO)
     *
     * @var string
     */
    protected $_undo;

    /**
     * The delta set which the revision belongs to.
     *
     * @var string
     */
    protected $_delta_set;

    /**
     * The number of the revision.
     *
     * @var int
     */
    protected $_revision_number;

    public function __construct($src, $delta_set, $revision_number) {
        $this->_delta_set = $delta_set;
        $this->_revision_number = $revision_number;
        list($this->_commit, $this->_undo) = $this->extract_commit_undo($src);
    }

    public function extract_commit_undo($src) {
        $commit = '';
        $undo = '';
        $current = 'commit';

        foreach (explode(PHP_EOL, $src) as $line) {
            if (strpos($line, '-- //@UNDO') !== FALSE) {
                if ($current === 'undo')
                    throw new Exception("2 or more '-- //@UNDO' found in: " . $src);

                $current = 'undo';
            } else {
                $$current .= $line;
            }
        }

        return array($commit, $undo);
    }

    public function __get($name) {
        static $enabled_attributes = array('commit', 'undo');
        if (in_array($name, $enabled_attributes))
            return $this->{'_' . $name};

        throw new Exception("property $name of class " . __CLASS__ . "does not exist or is not readable");
    }

}
