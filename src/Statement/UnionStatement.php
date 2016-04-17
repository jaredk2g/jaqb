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

use JAQB\Query\SelectQuery;

class UnionStatement extends Statement
{
    /**
     * @var array
     */
    protected $queries = [];

    /**
     * Adds a query to the statement.
     *
     * @param SelectQuery  $query
     * @param string|false $type
     *
     * @return self
     */
    public function addQuery(SelectQuery $query, $type = false)
    {
        $this->queries[] = [$query, $type];

        return $this;
    }

    /**
     * Gets the queries for this statement.
     *
     * @return array
     */
    public function getQueries()
    {
        return $this->queries;
    }

    public function build()
    {
        // build each query and concatenate
        $queries = [];
        $this->values = [];
        foreach ($this->queries as $row) {
            list($query, $type) = $row;

            $prefix = 'UNION ';
            if ($type) {
                $prefix .= $type.' ';
            }

            $queries[] = $prefix.$query->build();

            $this->values = array_merge($this->values, $query->getValues());
        }

        return implode(' ', $queries);
    }
}
