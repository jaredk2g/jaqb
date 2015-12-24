<?php

/**
 * @author Jared King <j@jaredtking.com>
 *
 * @link http://jaredtking.com
 *
 * @copyright 2015 Jared King
 * @license MIT
 */
namespace JAQB\Query;

use JAQB\Operations\Executable;
use JAQB\Operations\Fetchable;

class SqlQuery extends AbstractQuery
{
    use Executable, Fetchable;

    /**
     * @var string
     */
    protected $sql;

    /**
     * Sets the SQL for the query.
     *
     * @param string $sql
     *
     * @return self
     */
    public function raw($sql)
    {
        $this->sql = $sql;

        return $this;
    }

    /**
     * Sets the parameters for this query to be injected into
     * the prepared statement.
     *
     * @return self
     */
    public function parameters(array $values)
    {
        $this->values = $values;

        return $this;
    }

    public function build()
    {
        return $this->sql;
    }
}
