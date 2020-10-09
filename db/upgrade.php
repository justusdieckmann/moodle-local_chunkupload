<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Update script for chunkupload plugin
 *
 * @package local_chunkupload
 * @copyright  2020 Justus Dieckmann WWU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Update script for local_chunkupload.
 * @param int $oldversion Version id of the previously installed version.
 * @return bool
 * @throws ddl_exception
 * @throws ddl_table_missing_exception
 * @throws downgrade_exception
 * @throws upgrade_exception
 */
function xmldb_local_chunkupload_upgrade($oldversion) {

    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2020100900) {
        // Define table local_chunkupload_files to be dropped.
        $table = new xmldb_table('local_chunkupload_files');

        // Conditionally launch drop table for local_chunkupload_files.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Remove all (partially) uploaded files.
        remove_dir(\local_chunkupload\chunkupload_form_element::get_base_folder(), true);

        // Define table local_chunkupload_files to be created.
        $table = new xmldb_table('local_chunkupload_files');

        // Adding fields to table local_chunkupload_files.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('maxlength', XMLDB_TYPE_INTEGER, '15', null, null, null, null);
        $table->add_field('lastmodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('state', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('filename', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('length', XMLDB_TYPE_INTEGER, '15', null, null, null, null);
        $table->add_field('currentpos', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('token', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table local_chunkupload_files.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('unique_token', XMLDB_KEY_UNIQUE, ['token']);

        // Conditionally launch create table for local_chunkupload_files.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // local_chunkupload savepoint reached.
        upgrade_plugin_savepoint(true, 2020100900, 'tool', 'lifecycle');
    }

    return true;
}