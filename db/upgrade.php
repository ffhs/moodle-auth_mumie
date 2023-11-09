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
 * MUMIE Task upgrade function
 *
 * @package auth_mumie
 * @copyright  2017-2020 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * xmldb_auth_mumie_upgrade is the function that upgrades
 * the auth_mumie database when is needed
 *
 * This function is automatically called when version number in
 * version.php changes.
 *
 * @param int $oldversion New old version number.
 *
 * @return boolean
 */
function xmldb_auth_mumie_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2019110104) {
        // Code to add the column, generated by the 'View PHP Code' option of the XMLDB editor.

        $table = new xmldb_table("auth_mumie_id_hashes");
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, true);
        $table->add_field('the_user', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
        $table->add_field('hash', XMLDB_TYPE_CHAR, '128', null, XMLDB_NOTNULL);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        $table = new xmldb_table("auth_mumie_sso_tokens");
        $field = new xmldb_field('the_user', XMLDB_TYPE_CHAR, '128');

        $dbman->change_field_type($table, $field);
        $dbman->change_field_precision($table, $field);
        upgrade_plugin_savepoint(true, 2019110104, 'auth', 'mumie');
    }

    if ($oldversion < 2020011400) {
        $table = new xmldb_table("auth_mumie_servers");
        $field = new xmldb_field('name', XMLDB_TYPE_CHAR, '200');
        $dbman->change_field_precision($table, $field);

        $table = new xmldb_table("auth_mumie_id_hashes");
        $field = new xmldb_field('hash', XMLDB_TYPE_CHAR, '160');
        $dbman->change_field_precision($table, $field);

        $table = new xmldb_table("auth_mumie_sso_tokens");
        $field = new xmldb_field('the_user', XMLDB_TYPE_CHAR, '160');
        $dbman->change_field_precision($table, $field);
        upgrade_plugin_savepoint(true, 2020011400, 'auth', 'mumie');
    }

    if ($oldversion < 2023062000) {
        $table = new xmldb_table('auth_mumie_cryptographic_key');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('key', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        upgrade_plugin_savepoint(true, 2023062000, 'auth', 'mumie');
    }

    if ($oldversion < 2023110800) {
        $table = new xmldb_table('auth_mumie_cryptographic_key');
        $field = new xmldb_field('key');
        if ($dbman->field_exists($table, $field)) {
            $field->set_attributes(XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
            $dbman->rename_field($table, $field, 'keyvalue');
        }
        upgrade_plugin_savepoint(true, 2023110800, 'auth', 'mumie');
    }

    return true;
}
