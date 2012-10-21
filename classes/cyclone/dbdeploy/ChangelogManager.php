<?php
namespace cyclone\dbdeploy;

use cyclone\DB;
use cyclone\db\ConnectionException;

/**
 * <p>A ChangelogManager handles the changelog table which stores which revisions have been applied
 * to the schema. If a revision is applied then one row is inserted to the changelog table. If a revision
 * is deleted then its corresponding row is deleted from the table.</p>
 *
 * <p>The name of the changelog table is <code>changelog</code> and it has a composite
 *  <code>(change_number, delta_set)</code> primary key. The changelog table has the following schema:
 *      <table>
 *          <thead>
 *              <tr>
 *                  <th>column name</th>
 *                  <th>column DDL</th>
 *                  <th>description</th>
 *              </tr>
 *          </thead>
 *          <tbody>
 *              <tr>
 *                  <td>change_number</td>
 *                  <td>BIGINT NOT NULL</td>
 *                  <td>the applied revision number</td>
 *              </tr>
 *              <tr>
 *                  <td>delta_set</td>
 *                  <td>VARCHAR(10) NOT NULL</td>
 *                  <td>the delta set which the applied revision belongs to</td>
 *              </tr>
 *              <tr>
 *                  <td>start_dt</td>
 *                  <td>TIMESTAMP NOT NULL</td>
 *                  <td>the timestamp when the dbdeploy tool started to apply the revision</td>
 *              </tr>
 *              <tr>
 *                  <td>complete_dt</td>
 *                  <td>TIMESTAMP NOT NULL</td>
 *                  <td>the timestamp when the dbdeploy tool finished applying the revision</td>
 *              </tr>
 *              <tr>
 *                  <td>applied_by</td>
 *                  <td>VARCHAR(100) NOT NULL</td>
 *                  <td>the name of the tool which applied the revision. The dbdeploy tool of CyclonePHP
 *                      uses the 'cyclone-dbdeploy' string by default, but it can be changed by assigning
 *                      a new value to the @c ChangelogManager::$applied_by property.</td>
 *              </tr>
 *              <tr>
 *                  <td>description</td>
 *                  <td>VARCHAR(500) NOT NULL</td>
 *                  <td>the description of the revision</td>
 *              </tr>
 *          </tbody>
 *      </table>
 *      This schema is the same as the schema used by the dbdeploy task of Phing.
 * </p>
 *
 * @package dbdeploy
 * @author Bence Erős <crystal@cyclonephp.org>
 */
class ChangelogManager {

    public static $applied_by = 'cyclone-dbdeploy';

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
            'start_dt' => $now,
            'applied_by' => static::$applied_by,
            'description' => $rev->description
        ))->exec($this->_connection);
        try {
            DB::query($rev->commit)->exec($this->_connection);
        } catch (db\Exception $ex) {
            DB::delete($this->_changelog_table)
                ->where('change_number', '=', DB::esc($rev->revision_number))
                ->where('delta_set', '=', DB::esc($rev->delta_set))->exec($this->_connection);
            throw new Exception("failed to apply revision {$rev->revision_number} in delta set '{$rev->delta_set}'"
                , $ex->getCode()
                , $ex);
        }
        $now = date('Y-m-d H:i:s');
        DB::update($this->_changelog_table)->values(array(
            'complete_dt' => $now
        ))->where('change_number', '=', DB::esc($rev->revision_number))
          ->where('delta_set', '=', DB::esc($rev->delta_set))
          ->exec($this->_connection);
    }

    public function undo(Revision $rev) {
        try {
            DB::query($rev->undo)->exec($this->_connection);
            DB::delete($this->_changelog_table)
                ->where('change_number', '=', DB::esc($rev->revision_number))
                ->where('delta_set', '=', DB::esc($rev->delta_set))->exec($this->_connection);
        } catch (db\Exception $ex) {
            throw new Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function current($delta_set) {
        $result = DB::select(array(DB::expr('max(change_number)'), 'max'))
            ->from($this->_changelog_table)
            ->where('delta_set', '=', DB::esc($delta_set))
            ->exec($this->_connection)->get_single_row();
        return $result['max'];

    }
}