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

        if ( ! is_numeric($rev_from) || ! is_numeric($rev_to))
            throw new Exception('invalid revision format');

        $this->_source_reader->load_revisions($this->_delta_set);

        $revisions = Revision::get_by_delta_set($this->_delta_set);

        $rval = '';
        if ($rev_from < $rev_to) {
            for ($i = $rev_from + 1; $i <= $rev_to; ++$i) {
                $rval .= $revisions[$i]->commit . PHP_EOL;
            }
        } elseif ($rev_from > $rev_to) {
            for ($i = $rev_from; $i > $rev_to; --$i) {
                $rval .= $revisions[$i]->undo . PHP_EOL;
            }
        }
        return $rval;
    }

}
