<?php

/**
 * @author Jared King <j@jaredtking.com>
 *
 * @link http://jaredtking.com
 *
 * @copyright 2015 Jared King
 * @license MIT
 */
namespace JAQB\Statement;

class FromStatement extends Statement
{
    /**
     * @var bool
     */
    protected $hasFrom;

    /**
     * @var array
     */
    protected $tables = [];

    /**
     * @var array
     */
    protected $joins = [];

    /**
     * @param bool $hasFrom when true, statement is prefixed with `FROM`
     */
    public function __construct($hasFrom = true)
    {
        $this->hasFrom = $hasFrom;
    }

    /**
     * Tells whether this statement is prefixed with FROM.
     *
     * @return bool true: has FROM, false: no FROM
     */
    public function hasFrom()
    {
        return $this->hasFrom;
    }

    /**
     * Adds one or more tables to this statement.
     * Supported input styles:
     * - addTable('Table,Table2')
     * - addTable(['Table','Table2']).
     *
     * @param string|array $tables
     *
     * @return self
     */
    public function addTable($tables)
    {
        if (!is_array($tables)) {
            $tables = array_map(function ($t) {
                return trim($t);
            }, explode(',', $tables));
        }

        $this->tables = array_merge($this->tables, $tables);

        return $this;
    }

    /**
     * Adds a join condition to this statement
     * Supported input styles:
     * - addJoin('Table,Table2')
     * - addJoin(['Table','Table2']).
     *
     * @param string|array $tables table names
     * @param string       $on     ON condition
     * @param string       $using  USING columns
     * @param string       $type   join type, i.e. OUTER JOIN, CROSS JOIN
     *
     * @return self
     */
    public function addJoin($tables, $on = null, $using = null, $type = 'JOIN')
    {
        if (!is_array($tables)) {
            $tables = array_map(function ($t) {
                return trim($t);
            }, explode(',', $tables));
        }

        if ($using !== null) {
            $using = array_map(function ($column) {
                return trim($column);
            }, explode(',', $using));
        } else {
            $using = [];
        }

        $this->joins[] = [
            $type,
            $tables,
            $on,
            $using, ];

        return $this;
    }

    /**
     * Gets the table(s) associated with this statement.
     *
     * @return array
     */
    public function getTables()
    {
        return $this->tables;
    }

    /**
     * Gets the join(s) associated with this statement.
     *
     * @return array
     */
    public function getJoins()
    {
        return $this->joins;
    }

    public function build()
    {
        // tables
        $tables = $this->tables;
        foreach ($tables as &$table) {
            $table = $this->escapeIdentifier($table);
        }

        if (count($tables) == 0) {
            return '';
        }

        // joins
        $joins = $this->joins;
        foreach ($joins as &$join) {
            // table(s)
            foreach ($join[1] as &$table) {
                $table = $this->escapeIdentifier($table);
            }
            $join[1] = implode(',', array_filter($join[1]));

            // using clause
            foreach ($join[3] as &$column) {
                $column = $this->escapeIdentifier($column);
            }
            $join[3] = implode(',', array_filter($join[3]));

            if ($join[3]) {
                $join[3] = 'USING ('.$join[3].')';
            } else {
                unset($join[3]);
            }

            // on clause
            if ($join[2]) {
                $join[2] = 'ON '.$join[2];
            } else {
                unset($join[2]);
            }

            $join = implode(' ', $join);
        }
        $joins = implode(' ', array_filter($joins));

        return (($this->hasFrom) ? 'FROM ' : '').implode(',', array_filter($tables))
            .(($joins) ? " $joins" : '');
    }
}
