<?php

/**
 * @author Jared King <j@jaredtking.com>
 *
 * @see http://jaredtking.com
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
     * @param string $type
     *
     * @return self
     */
    public function addQuery(SelectQuery $query, $type = '')
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
        // reset the parameterized values
        $this->values = [];

        // build each select query and concatenate
        $queries = [];
        foreach ($this->queries as $row) {
            list($query, $type) = $row;

            if ($type) {
                $type .= ' ';
            }

            $queries[] = 'UNION '.$type.$query->build();

            $this->values = array_merge(
                $this->values,
                $query->getValues()
            );
        }

        return implode(' ', $queries);
    }
}
