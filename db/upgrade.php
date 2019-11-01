<?

defined('MOODLE_INTERNAL') || die();


function xmldb_auth_mumie_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2019110104) {
        // Code to add the column, generated by the 'View PHP Code' option of the XMLDB editor.

        $table =  new xmldb_table("auth_mumie_id_hashes");
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
        $table->add_field('hash', XMLDB_TYPE_CHAR, '128', null, XMLDB_NOTNULL);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        $table = new xmldb_table("auth_mumie_sso_tokens");
        $field = new xmldb_field('the_user', XMLDB_TYPE_CHAR, '128');

        $dbman->change_field_type($table, $field);
        $dbman->change_field_precision($table, $field);

    }

    return true;
}