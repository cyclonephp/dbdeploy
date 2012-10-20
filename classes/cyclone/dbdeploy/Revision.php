<?php

namespace cyclone\dbdeploy;

/**
 * Model class representing revisions (deltas).
 *
 * @author Bence Erős <crystal@cyclonephp.org>
 * @package dbdeploy
 * @property-read $commit string
 * @property-read $undo $string
 * @property-read $delta_set string
 * @property-read $revision_number int
 * @property-read $description string
 */
class Revision {

    protected static $_storage = array();

    /**
     * Returns the revision instances which belong to the given delta set.
     * The return value is an array of revision number =&gt; @c Revision instance
     * pairs.
     *
     * @param $delta_set
     * @return Revision
     * @throws Exception if the delta set is not loaded.
     */
    public static function get_by_delta_set($delta_set) {
        if ( ! isset(self::$_storage[$delta_set]))
            throw new Exception("delta set '$delta_set' is not loaded'");

        return self::$_storage[$delta_set];
    }

    /**
     * Queries the revision according to the given delta set and revision number.
     *
     * @param $delta_set string the name of the delta set
     * @param $revision_number
     * @return Revision
     * @throws Exception if the delta set is not loaded or the revision in the
     *  delta set is not loaded.
     * @uses get_by_delta_set()
     */
    public static function get_by($delta_set, $revision_number) {
        $delta_set_revisions = self::get_by_delta_set($delta_set);
        if ( ! isset($delta_set_revisions[$revision_number]))
            throw new Exception("revision '$revision_number' in delta set '$delta_set' is not loaded");

        return $delta_set_revisions[$revision_number];
    }

    /**
     * Clears the internal @c Revision instance storage.
     *
     * Only for unittests.
     */
    public static function clear_storage() {
        self::$_storage = array();
    }

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

    protected $_description;

    public function __construct($src, $delta_set, $revision_number, $description) {
        $this->_delta_set = $delta_set;
        $this->_revision_number = $revision_number;
        $this->_description = $description;
        list($this->_commit, $this->_undo) = $this->extract_commit_undo($src);
        if ( ! isset(self::$_storage[$delta_set])) {
            self::$_storage[$delta_set] = array();
        }

        if (isset(self::$_storage[$delta_set][$revision_number]))
            throw new Exception("invalid state: cannot create two Revision instances "
                 . "for revision #$revision_number in delta set '$delta_set'");

        self::$_storage[$delta_set][$revision_number] = $this;
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
        static $enabled_attributes = array('commit'
            , 'undo'
            , 'delta_set'
            , 'revision_number'
            , 'description'
        );
        if (in_array($name, $enabled_attributes))
            return $this->{'_' . $name};

        throw new \cyclone\PropertyAccessException(get_class($this), $name);
    }

}
