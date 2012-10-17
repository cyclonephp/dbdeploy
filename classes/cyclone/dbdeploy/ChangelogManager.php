<?php
namespace cyclone\dbdeploy;

use cyclone\DB;
use cyclone\db\ConnectionException;

class ChangelogManager {

    private $_connection;

    private $_changelog_table;

    public function __construct($connection, $changelog_table) {
        $this->_connection = $connection;
        $this->_changelog_table = $changelog_table;
        $this->init_db();
    }

    public function init_db() {
        try {
            DB::select()->from($this->_changelog_table)->limit(1)->exec($this->_connection);
        } catch (ConnectionException $ex) {
            echo "failed to establish database connection: " . $ex->getMessage() . \PHP_EOL;
            exit(1);
        } catch(db\Exception $ex) {
            DB::query("CREATE TABLE {$this->_changelog_table} (
                change_number BIGINT NOT NULL,
                delta_set VARCHAR(10) NOT NULL,
                start_dt TIMESTAMP NOT NULL,
                complete_dt TIMESTAMP NULL,
                applied_by VARCHAR(100) NOT NULL,
                description VARCHAR(500) NOT NULL,
                PRIMARY KEY(change_number, delta_set)
            );")->exec($this->_connection);
        }
    }

    public function apply(Revision $rev) {
        $now = date('Y-m-d H:i:s');
        DB::insert($this->_changelog_table)->values(array(
            'change_number' => $rev->revision_number,
            'delta_set' => $rev->delta_set,
            'start_dt' => $now
        ))->exec($this->_connection);
        DB::query($rev->commit)->exec($this->_connection);
        $now = date('Y-m-d H:i:s');
        DB::update($this->_changelog_table)->values(array(
            'complete_dt' => $now
        ))->where('change_number', '=', DB::esc($rev->revision_number))
          ->where('delta_set', '=', DB::esc($rev->delta_set))
          ->exec($this->_connection);
    }

    public function undo(Revision $rev) {
        DB::query($rev->undo)->exec($this->_connection);
        DB::delete($this->_changelog_table)
            ->where('change_number', '=', DB::esc($rev->revision_number))
            ->where('delta_set', '=', DB::esc($rev->delta_set))->exec($this->_connection);
    }

    public function current($delta_set) {
        $result = DB::select(array(DB::expr('max(change_number)'), 'max'))
            ->from($this->_changelog_table)
            ->where('delta_set', '=', DB::esc($delta_set))
            ->exec($this->_connection)->get_single_row();
        return $result['max'];

    }
}