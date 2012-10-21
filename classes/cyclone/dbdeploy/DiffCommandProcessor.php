<?php

namespace cyclone\dbdeploy;

use cyclone as cy;

/**
 * @author Bence Erős <crystal@cyclonephp.org>
 * @package dbdeploy
 */
class DiffCommandProcessor extends CommandProcessor {

    public function get_result() {
        $revisions = explode('..', $this->_revision);
        if (count($revisions) !== 2)
            throw new Exception('invalid revision format');

        list($rev_from, $rev_to) = $revisions;

        if ($rev_from < 0)
            throw new Exception("'$rev_from' is not a valid revision number");

        if ($rev_to < 0)
            throw new Exception("'$rev_to' is not a valid revision number");

        if ( ! is_numeric($rev_from) || ! is_numeric($rev_to))
            throw new Exception('invalid revision format');

        $revisions = Revision::get_by_delta_set($this->_delta_set);

        $rval = '';
        if ($rev_from < $rev_to) {
            for ($i = $rev_from + 1; $i <= $rev_to; ++$i) {
                if ( ! isset($revisions[$i]))
                    throw new Exception("revision $i in delta set {$this->_delta_set} does not exist");

                $rval .= $revisions[$i]->commit . PHP_EOL;
            }
        } elseif ($rev_from > $rev_to) {
            for ($i = $rev_from; $i > $rev_to; --$i) {
                if ( ! isset($revisions[$i]))
                    throw new Exception("revision $i in delta set {$this->_delta_set} does not exist");

                $rval .= $revisions[$i]->undo . PHP_EOL;
            }
        }
        return $rval;
    }

}
