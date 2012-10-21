<?php

namespace cyclone\dbdeploy;

use cyclone as cy;

/**
 * @author Bence Erős <crystal@cyclonephp.org>
 * @package dbdeploy
 */
class RevertCommandProcessor extends  CommandProcessor {

    public function get_result() {
        if (NULL === $this->_changelog_mgr) {
            $this->_changelog_mgr = new ChangelogManager($this->_connection, $this->_changelog_table);
        }

        if ( ! is_numeric($this->_revision) || $this->_revision < 0)
            throw new Exception("'{$this->_revision}' is not a valid revision number");

        $last_applied_rev = $this->_changelog_mgr->current($this->_delta_set);

        if ($last_applied_rev <= $this->_revision)
            throw new Exception("current revision is {$last_applied_rev}, cannot revert to {$this->_revision}");

        $rval = '';
        for ($i = $last_applied_rev; $i > $this->_revision; --$i) {
            $rev = Revision::get_by($this->_delta_set, $i);
            if ($this->_exec) {
                $this->_changelog_mgr->undo($rev);
            }
            if ( ! $this->_quiet) {
                $rval .= $rev->undo . \PHP_EOL;
            }
        }

        return $rval;
    }

}
