<?php

class wpdbx extends wpdb
{
    public function __construct()
    {
        parent::__construct(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);

        global $table_prefix;
        $this->set_prefix($table_prefix);
    }

    //--------------------------------------------------------------------------
    //----  Insert unique
    //--------------------------------------------------------------------------

    public function insert_unique($table, $data, $format = null)
    {
        $this->insert_id = 0;

        $data = $this->process_fields($table, $data, $format);
        if (false === $data) {
            return false;
        }

        $formats = array();
        $values = array();
        foreach ($data as $value) {
            if (is_null($value['value'])) {
                $formats[] = 'NULL';
                continue;
            }

            $formats[] = $value['format'];
            $values[] = $value['value'];
        }

        $fields = '`' . implode('`, `', array_keys($data)) . '`';
        $formats = implode(', ', $formats);

        $key = array_key_first($data);
        $plug = '`' . $key . '` = `' . $key . '`';

        $sql = "INSERT INTO `$table` ($fields) VALUES ($formats) ON DUPLICATE KEY UPDATE $plug";

        $this->check_current_query = false;
        return $this->query($this->prepare($sql, $values));
    }

    //--------------------------------------------------------------------------
    //----  Insert multiple
    //--------------------------------------------------------------------------

    public function insert_multiple($table, $data, $format = null)
    {
        $this->insert_id = 0;

        $formats = array();
        $values = array();

        foreach ($data as $index => $row) {
            $row = $this->process_fields($table, $row, $format);

            if ($row === false || array_keys($data[$index]) !== array_keys($data[0])) {
                wp_load_translations_early();
                _doing_it_wrong(
                    'wpdbx::insert_multiple',
                    sprintf(
                        /* translators: 1: Number of row, 2: Number of keys in row, 3: Number of keys passed in first row. */
                        __('The query row %1$d keys (%2$d) does not match the number of keys passed in first row (%3$d).', 'sadovod-misc'),
                        $index,
                        count(array_keys($data[$index])),
                        count(array_keys($data[0]))
                    ),
                    '0.1.3'
                );
            }

            $row_data = array();
            foreach ($row as $value) {
                if (is_null($value['value'])) {
                    $row_data[] = "NULL";
                    continue;
                }

                $row_data[] = $value['format'];
                $values[] = $value['value'];
            }

            $formats[] = '(' . implode(', ', $row_data) . ')';
        }

        $fields  = '`' . implode('`, `', array_keys($data[0])) . '`';
        $formats = implode(', ', $formats);
        $sql = "INSERT INTO `$table` ($fields) VALUES $formats";

        $this->check_current_query = false;
        return $this->query($this->prepare($sql, $values));
    }

    //--------------------------------------------------------------------------
    //----  Replace override
    //--------------------------------------------------------------------------

    public function replace($table, $data, $format = null, $replace = null, $replace_fromat = null)
    {
        if (!isset($replace) || empty($replace))
            return $this->insert_unique($table, $data, $format);

        $this->insert_id = 0;

        $data = $this->process_fields($table, $data, $format);
        if (false === $data) {
            return false;
        }

        $formats = array();
        $values = array();
        foreach ($data as $value) {
            if (is_null($value['value'])) {
                $formats[] = 'NULL';
                continue;
            }

            $formats[] = $value['format'];
            $values[] = $value['value'];
        }

        $fields = '`' . implode('`, `', array_keys($data)) . '`';
        $formats = implode(', ', $formats);

        $replace = $this->process_fields($table, $replace, $replace_fromat);
        if (false === $replace) {
            return false;
        }

        $replaces = array();
        foreach ($replace as $field => $value) {
            if (preg_match('(`[\w\-$]+`)', $value['value'], $mathes)) {
                $match = $mathes[0];
                $replaces[] = "`$field` = VALUES(" . $match . ")";
            } else {
                if (is_null($value['value'])) {
                    $replaces[] = "`$field` = NULL";
                    continue;
                }

                $replaces[] = "`$field` = " . $value['format'];
            }
        }

        $replaces = implode(', ', $replaces);
        $sql = "INSERT INTO `$table` ($fields) VALUES ($formats) ON DUPLICATE KEY UPDATE $replaces";

        $this->check_current_query = false;
        return $this->query($this->prepare($sql, $values));
    }

    //--------------------------------------------------------------------------
    //----  Replace multiple
    //--------------------------------------------------------------------------

    public function replace_multiple($table, $data, $format = null, $replace = null, $replace_fromat = null)
    {
        if (!isset($replace) || empty($replace))
            return $this->insert_multiple($table, $data, $format);

        $this->insert_id = 0;

        $formats = array();
        $values = array();

        foreach ($data as $index => $row) {
            $row = $this->process_fields($table, $row, $format);

            if ($row === false || array_keys($data[$index]) !== array_keys($data[0])) {
                wp_load_translations_early();
                _doing_it_wrong(
                    'wpdbx::insert_multiple',
                    sprintf(
                        /* translators: 1: Number of row, 2: Number of keys in row, 3: Number of keys passed in first row. */
                        __('The query row %1$d keys (%2$d) does not match the number of keys passed in first row (%3$d).', 'sadovod-misc'),
                        $index,
                        count(array_keys($data[$index])),
                        count(array_keys($data[0]))
                    ),
                    '0.1.3'
                );
            }

            $row_data = array();
            foreach ($row as $value) {
                if (is_null($value['value'])) {
                    $row_data[] = "NULL";
                    continue;
                }

                $row_data[] = $value['format'];
                $values[] = $value['value'];
            }

            $formats[] = '(' . implode(', ', $row_data) . ')';
        }

        $replace = $this->process_fields($table, $replace, $replace_fromat);
        if (false === $replace) {
            return false;
        }

        $replaces = array();
        foreach ($replace as $field => $value) {
            if (preg_match('(`[\w\-$]+`)', $value['value'], $mathes)) {
                $match = $mathes[0];
                $replaces[] = "`$field` = VALUES(" . $match . ")";
            } else {
                if (is_null($value['value'])) {
                    $replaces[] = "`$field` = NULL";
                    continue;
                }

                $replaces[] = "`$field` = " . $value['format'];
            }
        }

        $fields  = '`' . implode('`, `', array_keys($data[0])) . '`';
        $formats = implode(', ', $formats);
        $replaces = implode(', ', $replaces);
        $sql = "INSERT INTO `$table` ($fields) VALUES $formats ON DUPLICATE KEY UPDATE $replaces";
        $this->check_current_query = false;
        return $this->query($this->prepare($sql, $values));
    }

    //--------------------------------------------------------------------------
    //----  Update extended
    //--------------------------------------------------------------------------

    public function update_multiple($table, $data, $where, $data_format = null, $where_format = null)
    {
        if (!is_array($data) || !is_array($where)) {
            return false;
        }

        $data = $this->process_fields($table, $data, $data_format);
        if (false === $data) {
            return false;
        }
        $where = $this->process_fields($table, $where, $where_format);
        if (false === $where) {
            return false;
        }

        $fields     = array();
        $conditions = array();
        $values     = array();
        foreach ($data as $field => $value) {
            if (is_null($value['value'])) {
                $fields[] = "`$field` = NULL";
                continue;
            }

            $fields[] = "`$field` = " . $value['format'];
            $values[] = $value['value'];
        }

        foreach ($where as $field => $condition) {
            if (is_array($condition['value'])) {
                $size = count($condition['value']);

                $conditions[] = "`$field` IN (" . implode(',', array_fill(0, $size, $condition['format'])) . ")";
                $values = array_merge($values, $condition['value']);
                continue;
            }

            if (is_null($condition['value'])) {
                $conditions[] = "`$field` IS NULL";
                continue;
            }

            $conditions[] = "`$field` = " . $condition['format'];
            $values[]     = $condition['value'];
        }

        $fields     = implode(', ', $fields);
        $conditions = implode(' AND ', $conditions);

        $sql = "UPDATE `$table` SET $fields WHERE $conditions";

        $this->check_current_query = false;
        return $this->query($this->prepare($sql, $values));
    }

    //--------------------------------------------------------------------------
    //----  Remove multiple
    //--------------------------------------------------------------------------

    public function delete_multiple($table, $where, $format = null)
    {
        if (!is_array($where)) {
            return false;
        }

        $conditions = array();
        $values     = array();
        foreach ($where as $row) {
            $row = $this->process_fields($table, $row, $format);

            if ($row === false) {
                continue;
            }

            $row_where = array();
            foreach ($row as $field => $value) {
                if (is_null($value['value'])) {
                    $row_where[] = "`$field` IS NULL";
                    continue;
                }

                $row_where[] = "`$field` = " . $value['format'];
                $values[]     = $value['value'];
            }

            $conditions[] = '(' . implode(' AND ', $row_where) . ')';
        }

        $conditions = implode(' OR ', $conditions);
        $sql = "DELETE FROM `$table` WHERE $conditions";

        $this->check_current_query = false;
        return $this->query($this->prepare($sql, $values));
    }

    //--------------------------------------------------------------------------
    //----  Update Joined
    //--------------------------------------------------------------------------

    /**
     * Modififcation of wpdbx Update function.
     *
     * Originaly implemented for creating more advanced where cases.
     *
     * @see wpdbx::prepare()
     * @see wpdbx::$field_types
     * @see wp_set_wpdb_vars()
     *
     * @param string       $table        Table name.
     * 
     * @param array        $join         A named array of arrays with JOIN conditions (join table => [column => conditions]).
     * 
     * @param array        $data         Data to update (in column => value pairs).
     *                                   Both $data columns and $data values should be "raw" (neither should be SQL escaped).
     *                                   Sending a null value will cause the column to be set to NULL - the corresponding
     *                                   format is ignored in this case.
     * 
     * @param array        $where        A named array of WHERE clauses (in column => value pairs).
     *                                   Multiple clauses will be joined with ANDs.
     *                                   Both $where columns and $where values should be "raw".
     *                                   Sending a null value will create an IS NULL comparison - the corresponding
     *                                   format will be ignored in this case.
     * 
     * @param array|string $format       Optional. An array of formats to be mapped to each of the values in $data.
     *                                   If string, that format will be used for all of the values in $data.
     *                                   A format is one of '%d', '%f', '%s' (integer, float, string).
     *                                   If omitted, all values in $data will be treated as strings unless otherwise
     *                                   specified in wpdbx::$field_types.
     * 
     * @param array|string $where_format Optional. An array of formats to be mapped to each of the values in $where.
     *                                   If string, that format will be used for all of the items in $where.
     *                                   A format is one of '%d', '%f', '%s' (integer, float, string).
     *                                   If omitted, all values in $where will be treated as strings.
     * 
     * @return int|false The number of rows updated, or false on error.
     */
    public function update_joined($table, $join, $data, $where, $data_format = null, $where_format = null)
    {
        if (!is_array($join) || !is_array($data) || !is_array($where)) {
            return false;
        }

        //-------------------------------------------------------------------------------

        $join = $this->sanitize_joins($join);
        if (false === $join) {
            return false;
        }

        $joins = array();
        foreach ($join as $ctable => $case) {
            if (!is_array($case))
                continue;

            $joins[] = $join['join'] . ' JOIN `' . $ctable . '` ON ' . $this->get_sql_for_joins($table, $ctable, $case) . ' ';
        }

        //-------------------------------------------------------------------------------

        $data = $this->process_fields($table, $data, $data_format);
        if (false === $data) {
            return false;
        }
        $where = $this->process_fields($table, $where, $where_format);
        if (false === $where) {
            return false;
        }

        $fields     = array();
        $conditions = array();
        $values     = array();
        foreach ($data as $field => $value) {
            if (is_null($value['value'])) {
                $fields[] = "`$field` = NULL";
                continue;
            }

            $fields[] = "`$field` = " . $value['format'];
            $values[] = $value['value'];
        }
        foreach ($where as $field => $value) {
            if (is_null($value['value'])) {
                $conditions[] = "`$field` IS NULL";
                continue;
            }

            $conditions[] = "`$field` = " . $value['format'];
            $values[]     = $value['value'];
        }

        $joins      = implode(' AND ', $joins);
        $fields     = implode(', ', $fields);
        $conditions = implode(' AND ', $conditions);

        $sql = "UPDATE `$table` $joins SET $fields WHERE $conditions";

        $this->check_current_query = false;
        return $this->query($this->prepare($sql, $values));
    }

    public function get_sql_for_joins($table, $ctable, $case, $depth = 0)
    {
        $joins = array();

        foreach ($case as $data) {
            if (!is_array($data))
                continue;

            if ($this->is_case($data)) {

                $column = $data['column'];
                $compare = $data['compare'];
                $value = $data['value'];

                if ('LIKE' === $compare && $data['matches'] == 'value') {
                    $joins[] = $this->prepare(" $ctable.$column LIKE %s ", '%' . $this->esc_like($value) . '%');
                } else {
                    if ($data['matches'] == 'value') {
                        $joins[] = $this->prepare(" $ctable.$column $compare %s ", $value);
                    } else {
                        $joins[] = " $ctable.$column $compare $table.$value ";
                    }
                }
            } else {
                $joins[] = $this->get_sql_for_joins($table, $ctable, $data, ++$depth);
            }
        }

        if (false === $joins) {
            return false;
        }

        $joins = '(' . implode($case['relation'], $joins) . ')';
        return $joins;
    }

    public function sanitize_joins($queries, $depth = 0)
    {
        if (!is_array($queries))
            return array();

        $join_operators = array(
            'INNER',
            'LEFT',
            'RIGHT',
        );

        $non_numeric_operators = array(
            '=',
            '!=',
            'LIKE',
            'NOT LIKE',
            'IN',
            'NOT IN',
            'EXISTS',
            'NOT EXISTS',
            'RLIKE',
            'REGEXP',
            'NOT REGEXP',
        );

        $numeric_operators = array(
            '>',
            '>=',
            '<',
            '<=',
            'BETWEEN',
            'NOT BETWEEN',
        );

        if ($depth == 0) {
            if (isset($queries['join'])) {
                $queries['join'] = strtoupper($queries['join']);

                if (!in_array($queries['join'], $join_operators, true)) {
                    $clause['join'] = 'INNER';
                }
            } else {
                $queries['join'] = 'INNER';
            }
        }

        if (isset($queries['relation'])) {
            $queries['relation'] == 'AND' ? 'AND' : 'OR';
        } else {
            $queries['relation'] = 'AND';
        }

        foreach ($queries as $key => $query) {
            if (!is_array($query))
                continue;

            if ($this->is_case($query)) {
                if (!isset($query['matches']))
                    $queries[$key]['matches'] == 'value';

                if (isset($query['compare'])) {
                    $queries[$key]['compare'] = strtoupper($query['compare']);

                    if ($query['matches'] == 'value') {
                        if (!in_array($query['compare'], $numeric_operators, true)) {
                            $queries[$key]['compare'] = '=';
                        }
                    } else {
                        if (!in_array($query['compare'], $non_numeric_operators, true)) {
                            $queries[$key]['compare'] = '=';
                        }
                    }
                } else {
                    if ($query['matches'] == 'value') {
                        $queries[$key]['compare'] = isset($query['value']) && is_array($query['value']) ? 'IN' : '=';
                    } else {
                        $queries[$key]['compare'] = '=';
                    }
                }
            } else {
                $case = $this->sanitize_joins($query, ++$depth);

                if (!empty($case)) {
                    $queries[$key] = $case;
                }
            }
        }

        if (empty($queries))
            return array();

        return $queries;
    }

    /**
     * Determine is this a case block.
     *
     * A case block is one that has either a 'key' or
     * a 'value' array key.
     *
     *
     * @param array $query Join query arguments.
     * @return bool Its a case block or not.
     */
    protected function is_case($query)
    {
        return isset($query['key']) || isset($query['value']);
    }

    //--------------------------------------------------------------------------
    //----  Prepeare fix
    //--------------------------------------------------------------------------

    /**
     * Prepares a SQL query for safe execution. 
     * 
     * Modififcated to allow using of numbered placeholder. Because originally has bug counting its number
     *
     * Uses sprintf()-like syntax. The following placeholders can be used in the query string:
     *
     * - %d (integer)
     * - %f (float)
     * - %s (string)
     *
     * All placeholders MUST be left unquoted in the query string. A corresponding argument
     * MUST be passed for each placeholder.
     *
     * Note: There is one exception to the above: for compatibility with old behavior,
     * numbered or formatted string placeholders (eg, `%1$s`, `%5s`) will not have quotes
     * added by this function, so should be passed with appropriate quotes around them.
     *
     * Literal percentage signs (`%`) in the query string must be written as `%%`. Percentage wildcards
     * (for example, to use in LIKE syntax) must be passed via a substitution argument containing
     * the complete LIKE string, these cannot be inserted directly in the query string.
     * Also see wpdbx::esc_like().
     *
     * Arguments may be passed as individual arguments to the method, or as a single array
     * containing all arguments. A combination of the two is not supported.
     *
     * Examples:
     *
     *     $wpdbx->prepare( "SELECT * FROM `table` WHERE `column` = %s AND `field` = %d OR `other_field` LIKE %s", array( 'foo', 1337, '%bar' ) );
     *     $wpdbx->prepare( "SELECT DATE_FORMAT(`field`, '%%c') FROM `table` WHERE `column` = %s", 'foo' );
     *
     * @since 2.3.0
     * @since 5.3.0 Formalized the existing and already documented `...$args` parameter
     *              by updating the function signature. The second parameter was changed
     *              from `$args` to `...$args`.
     *
     * @link https://www.php.net/sprintf Description of syntax.
     *
     * @param string      $query   Query statement with sprintf()-like placeholders.
     * @param array|mixed $args    The array of variables to substitute into the query's placeholders
     *                             if being called with an array of arguments, or the first variable
     *                             to substitute into the query's placeholders if being called with
     *                             individual arguments.
     * @param mixed       ...$args Further variables to substitute into the query's placeholders
     *                             if being called with individual arguments.
     * @return string|void Sanitized query string, if there is a query to prepare.
     */
    public function prepare($query, ...$args)
    {
        if (is_null($query)) {
            return;
        }

        // This is not meant to be foolproof -- but it will catch obviously incorrect usage.
        if (strpos($query, '%') === false) {
            wp_load_translations_early();
            _doing_it_wrong(
                'wpdbx::prepare',
                sprintf(
                    /* translators: %s: wpdbx::prepare() */
                    __('The query argument of %s must have a placeholder.'),
                    'wpdbx::prepare()'
                ),
                '3.9.0'
            );
        }

        // If args were passed as an array (as in vsprintf), move them up.
        $passed_as_array = false;
        if (isset($args[0]) && is_array($args[0]) && 1 === count($args)) {
            $passed_as_array = true;
            $args            = $args[0];
        }

        foreach ($args as $arg) {
            if (!is_scalar($arg) && !is_null($arg)) {
                wp_load_translations_early();
                _doing_it_wrong(
                    'wpdbx::prepare',
                    sprintf(
                        /* translators: %s: Value type. */
                        __('Unsupported value type (%s).'),
                        gettype($arg)
                    ),
                    '4.8.2'
                );
            }
        }

        /*
     * Specify the formatting allowed in a placeholder. The following are allowed:
     *
     * - Sign specifier. eg, $+d
     * - Numbered placeholders. eg, %1$s
     * - Padding specifier, including custom padding characters. eg, %05s, %'#5s
     * - Alignment specifier. eg, %05-s
     * - Precision specifier. eg, %.2f
     */
        $allowed_format = '(?:[1-9][0-9]*[$])?[-+0-9]*(?: |0|\'.)?[-+0-9]*(?:\.[0-9]+)?';

        /*
     * If a %s placeholder already has quotes around it, removing the existing quotes and re-inserting them
     * ensures the quotes are consistent.
     *
     * For backward compatibility, this is only applied to %s, and not to placeholders like %1$s, which are frequently
     * used in the middle of longer strings, or as table name placeholders.
     */
        $query = str_replace("'%s'", '%s', $query); // Strip any existing single quotes.
        $query = str_replace('"%s"', '%s', $query); // Strip any existing double quotes.
        $query = preg_replace('/(?<!%)%s/', "'%s'", $query); // Quote the strings, avoiding escaped strings like %%s.

        $query = preg_replace("/(?<!%)(%($allowed_format)?f)/", '%\\2F', $query); // Force floats to be locale-unaware.

        $query = preg_replace("/%(?:%|$|(?!($allowed_format)?[sdF]))/", '%%\\1', $query); // Escape any unescaped percents.

        // Count the number of valid placeholders in the query.
        $placeholders = preg_match_all("/(^|[^%]|(%%)+)%($allowed_format)?[sdF]/", $query, $matches);

        // Exclude numbered placeholders.
        $numbered_placeholders = array();
        foreach ($matches[0] as $placeholder) {
            if (preg_match('/%[1-9]\$[sdF]/', $placeholder, $match)) {
                if (isset($numbered_placeholders[$match[0]]))
                    $placeholders--;

                $numbered_placeholders[$match[0]] = true;
            }
        }

        // Count the number of passed arguments in the query.
        $args_count = count($args);

        if (count($numbered_placeholders) !== $placeholders and $args_count !== $placeholders) {
            if (1 === $placeholders && $passed_as_array) {
                // If the passed query only expected one argument, but the wrong number of arguments were sent as an array, bail.
                wp_load_translations_early();
                _doing_it_wrong(
                    'wpdbx::prepare',
                    __('The query only expected one placeholder, but an array of multiple placeholders was sent.'),
                    '4.9.0'
                );

                return;
            } else {
                /*
             * If we don't have the right number of placeholders, but they were passed as individual arguments,
             * or we were expecting multiple arguments in an array, throw a warning.
             */
                wp_load_translations_early();
                _doing_it_wrong(
                    'wpdbx::prepare',
                    sprintf(
                        /* translators: 1: Number of placeholders, 2: Number of arguments passed. */
                        __('The query does not contain the correct number of placeholders (%1$d) for the number of arguments passed (%2$d).'),
                        $placeholders,
                        $args_count
                    ),
                    '4.8.3'
                );

                /*
             * If we don't have enough arguments to match the placeholders,
             * return an empty string to avoid a fatal error on PHP 8.
             */
                if ($args_count < $placeholders) {
                    $max_numbered_placeholder = !empty($matches[3]) ? max(array_map('intval', $matches[3])) : 0;

                    if (!$max_numbered_placeholder || $args_count < $max_numbered_placeholder) {
                        return '';
                    }
                }
            }
        }

        array_walk($args, array($this, 'escape_by_ref'));
        $query = vsprintf($query, $args);

        return $this->add_placeholder_escape($query);
    }

    /**
     * Prepares arrays of value/format pairs as passed to wpdb CRUD methods.
     *
     * @since 4.2.0
     * @since 0.3 Fix to allow use of nulls and numbered placeholders
     *
     * @param array $data   Array of fields to values.
     * @param mixed $format Formats to be mapped to the values in $data.
     * @return array Array, keyed by field names with values being an array
     *               of 'value' and 'format' keys.
     */
    protected function process_field_formats($data, $format)
    {
        $formats = (array)$format;
        $original_formats = $formats;

        foreach ($data as $field => $value) {
            $value = array(
                'value' => $value,
                'format' => '%s',
            );

            if (!empty($format)) {
                $value['format'] = array_shift($formats);
                if (!$value['format']) {
                    $value['format'] = reset($original_formats);
                }
            } elseif (isset($this->field_types[$field])) {
                $value['format'] = $this->field_types[$field];
            }

            if ($value['value'] === null) {
                foreach ($formats as $i => $placeholder) {
                    if (preg_match('/%(\d+)\$s/', $placeholder, $matches)) {
                        $match = parse_int($matches[1]);
                        $formats[$i] = str_replace('%' . $match . '$s', '%' . $match - 1 . '$s', $placeholder);
                    }
                }
            }

            $data[$field] = $value;
        }

        return $data;
    }
}

global $wpdbx;
$wpdbx = new wpdbx();
