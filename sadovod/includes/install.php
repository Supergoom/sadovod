<?php

if (!is_customize_preview() and !wp_is_json_request() and !is_admin() and !function_exists('dbDelta')) {

    /**
     * Create or updates tables if needed.
     */
    function install_sadovod()
    {
        global $wpdbx;

        $theme_version = wp_get_theme()->get('Version');
        $db_vesion = get_option('sadovod_db_version');

        $charset = $wpdbx->get_charset_collate();

        if ($theme_version != $db_vesion) {
            require_once ABSPATH . 'wp-admin/includes/schema.php';

            //----------------- Отчеты -----------------
            $sql = 'CREATE TABLE ' . $wpdbx->prefix . 'reports' . ' (
                report_id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,  
                user_id bigint(20) UNSIGNED NOT NULL,
                report_type int(1) UNSIGNED NOT NULL DEFAULT 0,
                report_title varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci,
                report_text longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci,
                report_status int(1) UNSIGNED NOT NULL DEFAULT 0,
                report_date datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (report_id)
            )' . $charset . ';';

            dbDelta($sql);

            //--------------------------------------------------
            update_option('sadovod_db_version', $theme_version);
        }
    }
    add_action('init', 'install_sadovod');

    function dbDelta($queries = '', $execute = true)
    {
        global $wpdbx;

        if (in_array($queries, array('', 'all', 'blog', 'global', 'ms_global'), true)) {
            $queries = wp_get_db_schema($queries);
        }

        if (!is_array($queries)) {
            $queries = explode(';', $queries);
            $queries = array_filter($queries);
        }

        $queries = apply_filters('dbdelta_queries', $queries);

        $cqueries = array(); // Creation queries.
        $iqueries = array(); // Insertion queries.
        $for_update = array();

        // Create a tablename index for an array ($cqueries) of queries.
        foreach ($queries as $qry) {
            if (preg_match('|CREATE TABLE ([^ ]*)|', $qry, $matches)) {
                $cqueries[trim($matches[1], '`')] = $qry;
                $for_update[$matches[1]] = 'Created table ' . $matches[1];
            } elseif (preg_match('|CREATE DATABASE ([^ ]*)|', $qry, $matches)) {
                array_unshift($cqueries, $qry);
            } elseif (preg_match('|INSERT INTO ([^ ]*)|', $qry, $matches)) {
                $iqueries[] = $qry;
            } elseif (preg_match('|UPDATE ([^ ]*)|', $qry, $matches)) {
                $iqueries[] = $qry;
            } else {
                // Unrecognized query type.
            }
        }

        $cqueries = apply_filters('dbdelta_create_queries', $cqueries);

        $iqueries = apply_filters('dbdelta_insert_queries', $iqueries);

        $text_fields = array('tinytext', 'text', 'mediumtext', 'longtext');
        $blob_fields = array('tinyblob', 'blob', 'mediumblob', 'longblob');

        $global_tables = $wpdbx->tables('global');
        foreach ($cqueries as $table => $qry) {
            // Upgrade global tables only for the main site. Don't upgrade at all if conditions are not optimal.
            if (in_array($table, $global_tables, true) && !wp_should_upgrade_global_tables()) {
                unset($cqueries[$table], $for_update[$table]);
                continue;
            }

            // Fetch the table column structure from the database.
            $suppress = $wpdbx->suppress_errors();
            $tablefields = $wpdbx->get_results("DESCRIBE {$table};");
            $wpdbx->suppress_errors($suppress);

            if (!$tablefields) {
                continue;
            }

            // Clear the field and index arrays.
            $cfields = array();
            $indices = array();
            $indices_without_subparts = array();

            // Get all of the field names in the query from between the parentheses.
            preg_match('|\((.*)\)|ms', $qry, $match2);
            $qryline = trim($match2[1]);

            // Separate field lines into an array.
            $flds = explode("\n", $qryline);

            // For every field line specified in the query.
            foreach ($flds as $fld) {
                $fld = trim($fld, " \t\n\r\0\x0B,"); // Default trim characters, plus ','.

                // Extract the field name.
                preg_match('|^([^ ]*)|', $fld, $fvals);
                $fieldname = trim($fvals[1], '`');
                $fieldname_lowercased = strtolower($fieldname);

                // Verify the found field name.
                $validfield = true;
                switch ($fieldname_lowercased) {
                    case '':
                    case 'primary':
                    case 'index':
                    case 'fulltext':
                    case 'unique':
                    case 'key':
                    case 'spatial':
                        $validfield = false;

                        preg_match(
                            '/^'
                                . '(?P<index_type>' // 1) Type of the index.
                                . 'PRIMARY\s+KEY|(?:UNIQUE|FULLTEXT|SPATIAL)\s+(?:KEY|INDEX)|KEY|INDEX'
                                . ')'
                                . '\s+' // Followed by at least one white space character.
                                . '(?:' // Name of the index. Optional if type is PRIMARY KEY.
                                . '`?' // Name can be escaped with a backtick.
                                . '(?P<index_name>' // 2) Name of the index.
                                . '(?:[0-9a-zA-Z$_-]|[\xC2-\xDF][\x80-\xBF])+'
                                . ')'
                                . '`?' // Name can be escaped with a backtick.
                                . '\s+' // Followed by at least one white space character.
                                . ')*'
                                . '\(' // Opening bracket for the columns.
                                . '(?P<index_columns>'
                                . '.+?' // 3) Column names, index prefixes, and orders.
                                . ')'
                                . '\)' // Closing bracket for the columns.
                                . '$/im',
                            $fld,
                            $index_matches
                        );
                        // phpcs:enable

                        // Uppercase the index type and normalize space characters.
                        $index_type = strtoupper(preg_replace('/\s+/', ' ', trim($index_matches['index_type'])));

                        // 'INDEX' is a synonym for 'KEY', standardize on 'KEY'.
                        $index_type = str_replace('INDEX', 'KEY', $index_type);

                        // Escape the index name with backticks. An index for a primary key has no name.
                        $index_name = ('PRIMARY KEY' === $index_type) ? '' : '`' . strtolower($index_matches['index_name']) . '`';

                        // Parse the columns. Multiple columns are separated by a comma.
                        $index_columns = array_map('trim', explode(',', $index_matches['index_columns']));
                        $index_columns_without_subparts = $index_columns;

                        // Normalize columns.
                        foreach ($index_columns as $id => &$index_column) {
                            // Extract column name and number of indexed characters (sub_part).
                            preg_match(
                                '/'
                                    . '`?' // Name can be escaped with a backtick.
                                    . '(?P<column_name>' // 1) Name of the column.
                                    . '(?:[0-9a-zA-Z$_-]|[\xC2-\xDF][\x80-\xBF])+'
                                    . ')'
                                    . '`?' // Name can be escaped with a backtick.
                                    . '(?:' // Optional sub part.
                                    . '\s*' // Optional white space character between name and opening bracket.
                                    . '\(' // Opening bracket for the sub part.
                                    . '\s*' // Optional white space character after opening bracket.
                                    . '(?P<sub_part>'
                                    . '\d+' // 2) Number of indexed characters.
                                    . ')'
                                    . '\s*' // Optional white space character before closing bracket.
                                    . '\)' // Closing bracket for the sub part.
                                    . ')?'
                                    . '/',
                                $index_column,
                                $index_column_matches
                            );

                            // Escape the column name with backticks.
                            $index_column = '`' . $index_column_matches['column_name'] . '`';

                            // We don't need to add the subpart to $index_columns_without_subparts
                            $index_columns_without_subparts[$id] = $index_column;

                            // Append the optional sup part with the number of indexed characters.
                            if (isset($index_column_matches['sub_part'])) {
                                $index_column .= '(' . $index_column_matches['sub_part'] . ')';
                            }
                        }

                        // Build the normalized index definition and add it to the list of indices.
                        $indices[] = "{$index_type} {$index_name} (" . implode(',', $index_columns) . ')';
                        $indices_without_subparts[] = "{$index_type} {$index_name} (" . implode(',', $index_columns_without_subparts) . ')';

                        // Destroy no longer needed variables.
                        unset($index_column, $index_column_matches, $index_matches, $index_type, $index_name, $index_columns, $index_columns_without_subparts);

                        break;
                }

                // If it's a valid field, add it to the field array.
                if ($validfield) {
                    $cfields[$fieldname_lowercased] = $fld;
                }
            }

            // For every field in the table.
            foreach ($tablefields as $tablefield) {
                $tablefield_field_lowercased = strtolower($tablefield->Field);
                $tablefield_type_lowercased = strtolower($tablefield->Type);

                // If the table field exists in the field array...
                if (array_key_exists($tablefield_field_lowercased, $cfields)) {

                    // Get the field type from the query.
                    preg_match('|`?' . $tablefield->Field . '`? ([^ ]*( unsigned)?)|i', $cfields[$tablefield_field_lowercased], $matches);
                    $fieldtype = $matches[1];
                    $fieldtype_lowercased = strtolower($fieldtype);

                    // Is actual field type different from the field type in query?
                    if ($tablefield->Type != $fieldtype) {
                        $do_change = true;
                        if (in_array($fieldtype_lowercased, $text_fields, true) && in_array($tablefield_type_lowercased, $text_fields, true)) {
                            if (array_search($fieldtype_lowercased, $text_fields, true) < array_search($tablefield_type_lowercased, $text_fields, true)) {
                                $do_change = false;
                            }
                        }

                        if (in_array($fieldtype_lowercased, $blob_fields, true) && in_array($tablefield_type_lowercased, $blob_fields, true)) {
                            if (array_search($fieldtype_lowercased, $blob_fields, true) < array_search($tablefield_type_lowercased, $blob_fields, true)) {
                                $do_change = false;
                            }
                        }

                        if ($do_change) {
                            // Add a query to change the column type.
                            $cqueries[] = "ALTER TABLE {$table} CHANGE COLUMN `{$tablefield->Field}` " . $cfields[$tablefield_field_lowercased];

                            $for_update[$table . '.' . $tablefield->Field] = "Changed type of {$table}.{$tablefield->Field} from {$tablefield->Type} to {$fieldtype}";
                        }
                    }

                    // Get the default value from the array.
                    if (preg_match("| DEFAULT '(.*?)'|i", $cfields[$tablefield_field_lowercased], $matches)) {
                        $default_value = $matches[1];
                        if ($tablefield->Default != $default_value) {
                            // Add a query to change the column's default value
                            $cqueries[] = "ALTER TABLE {$table} ALTER COLUMN `{$tablefield->Field}` SET DEFAULT '{$default_value}'";

                            $for_update[$table . '.' . $tablefield->Field] = "Changed default value of {$table}.{$tablefield->Field} from {$tablefield->Default} to {$default_value}";
                        }
                    }

                    // Remove the field from the array (so it's not added).
                    unset($cfields[$tablefield_field_lowercased]);
                } else {
                    // This field exists in the table, but not in the creation queries?
                }
            }

            // For every remaining field specified for the table.
            foreach ($cfields as $fieldname => $fielddef) {
                // Push a query line into $cqueries that adds the field to that table.
                $cqueries[] = "ALTER TABLE {$table} ADD COLUMN $fielddef";

                $for_update[$table . '.' . $fieldname] = 'Added column ' . $table . '.' . $fieldname;
            }

            // Index stuff goes here. Fetch the table index structure from the database.
            $tableindices = $wpdbx->get_results("SHOW INDEX FROM {$table};");

            if ($tableindices) {
                // Clear the index array.
                $index_ary = array();

                // For every index in the table.
                foreach ($tableindices as $tableindex) {
                    $keyname = strtolower($tableindex->Key_name);

                    // Add the index to the index data array.
                    $index_ary[$keyname]['columns'][] = array(
                        'fieldname' => $tableindex->Column_name,
                        'subpart' => $tableindex->Sub_part,
                    );
                    $index_ary[$keyname]['unique'] = (0 == $tableindex->Non_unique) ? true : false;
                    $index_ary[$keyname]['index_type'] = $tableindex->Index_type;
                }

                // For each actual index in the index array.
                foreach ($index_ary as $index_name => $index_data) {

                    // Build a create string to compare to the query.
                    $index_string = '';
                    if ('primary' === $index_name) {
                        $index_string .= 'PRIMARY ';
                    } elseif ($index_data['unique']) {
                        $index_string .= 'UNIQUE ';
                    }
                    if ('FULLTEXT' === strtoupper($index_data['index_type'])) {
                        $index_string .= 'FULLTEXT ';
                    }
                    if ('SPATIAL' === strtoupper($index_data['index_type'])) {
                        $index_string .= 'SPATIAL ';
                    }
                    $index_string .= 'KEY ';
                    if ('primary' !== $index_name) {
                        $index_string .= '`' . $index_name . '`';
                    }
                    $index_columns = '';

                    // For each column in the index.
                    foreach ($index_data['columns'] as $column_data) {
                        if ('' !== $index_columns) {
                            $index_columns .= ',';
                        }

                        // Add the field to the column list string.
                        $index_columns .= '`' . $column_data['fieldname'] . '`';
                    }

                    // Add the column list to the index create string.
                    $index_string .= " ($index_columns)";

                    // Check if the index definition exists, ignoring subparts.
                    $aindex = array_search($index_string, $indices_without_subparts, true);
                    if (false !== $aindex) {
                        // If the index already exists (even with different subparts), we don't need to create it.
                        unset($indices_without_subparts[$aindex]);
                        unset($indices[$aindex]);
                    }
                }
            }

            // For every remaining index specified for the table.
            foreach ((array)$indices as $index) {
                // Push a query line into $cqueries that adds the index to that table.
                $cqueries[] = "ALTER TABLE {$table} ADD $index";

                $for_update[] = 'Added index ' . $table . ' ' . $index;
            }

            // Remove the original table creation query from processing.
            unset($cqueries[$table], $for_update[$table]);
        }

        $allqueries = array_merge($cqueries, $iqueries);

        if ($execute) {
            foreach ($allqueries as $query) {
                $wpdbx->query($query);
            }
        }

        return $for_update;
    }
}

function register_public_posts()
{
    global $all_posts;
    global $public_posts;

    $all_posts = $public_posts = get_post_types(array('public' => true), 'objects');

    unset($public_posts['page']);
    unset($public_posts['attachment']);

    $all_posts['post'] = $public_posts['post'] = new WP_Post_Type('post', array('rewrite' => array('slug' => 'post')));
}
add_action('init', 'register_public_posts', 1000);
add_action('admin_init', 'register_public_posts', 1000);