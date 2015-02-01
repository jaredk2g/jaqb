<?php

/**
 * @package JAQB
 * @author Jared King <j@jaredtking.com>
 * @link http://jaredtking.com
 * @copyright 2015 Jared King
 * @license MIT
 */

namespace JAQB\Statement;

class FromStatement extends Statement
{
    /**
     * @var boolean
     */
    protected $hasFrom;

    /**
     * @var array
     */
    protected $tables = [];

    /**
     * @param boolean $hasFrom when true, statement is prefixed with `FROM`
     */
    public function __construct($hasFrom = true)
    {
        $this->hasFrom = $hasFrom;
    }

    /**
     * Tells whether this statement is prefixed with FROM
     *
     * @return boolean true: has FROM, false: no FROM
     */
    public function hasFrom()
    {
        return $this->hasFrom;
    }

    /**
     * Adds one or more tables to this statement.
     * Supported input styles:
     * - addTable('Table,Table2')
     * - addTable(['Table','Table2'])
     *
     * @param string|array $fields
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
     * Gets the table(s) associated with this statement
     *
     * @return array
     */
    public function getTables()
    {
        return $this->tables;
    }

    /**
     * Generates the raw SQL string for the statement
     *
     * @return string
     */
    public function build()
    {
        $tables = $this->tables;
        foreach ($tables as &$table) {
            $table = $this->escapeIdentifier($table);
        }

        // remove empty values
        $tables = array_filter($tables);

        if (count($tables) == 0) {
            return '';
        }

        return (($this->hasFrom) ? 'FROM ' : '').implode(',', $tables);
    }
}
