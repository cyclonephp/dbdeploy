<?php

namespace cyclone\dbdeploy;


/**
 * @package dbdeploy
 * @author Bence Erős <crystal@cyclonephp.org>
 */
class ApplyCommandProcessor extends CommandProcessor {

    public function get_result() {
        if (NULL === $this->_changelog_mgr) {
            $this->_changelog_mgr = new ChangelogManager($this->_connection, $this->_changelog_table);
        }

        if ( ! is_numeric($this->_revision) || $this->_revision < 0)
            throw new Exception("'{$this->_revision}' is not a valid revision number");

        $last_applied_rev = $this->_changelog_mgr->current($this->_delta_set);
        if ($last_applied_rev > $this->_revision)
            throw new Exception("cannot apply revision {$this->_revision} since the latest applied revision is $last_applied_rev");

        $latest_rev = $this->_source_reader->latest_revision($this->_delta_set);
        if ($this->_revision > $latest_rev)
            throw new Exception("cannot apply revision {$this->_revision} since the latest existing revision is $latest_rev");

        $rval = '';
        for ($i = $last_applied_rev + 1; $i <= $this->_revision; ++$i) {
            $revision = Revision::get_by($this->_delta_set, $i);
            if ($this->_exec) {
                $this->_changelog_mgr->apply($revision);
            }
            if ( ! $this->_quiet) {
                $rval .= $revision->commit . \PHP_EOL;
            }
        }

        return $rval;
    }

}
