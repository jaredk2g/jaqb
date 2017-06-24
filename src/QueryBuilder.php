<?php

/**
 * @author Jared King <j@jaredtking.com>
 *
 * @see http://jaredtking.com
 *
 * @copyright 2015 Jared King
 * @license MIT
 */

namespace JAQB;

use PDO;

class QueryBuilder
{
    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo = null)
    {
        $this->pdo = $pdo;
    }

    /**
     * Returns the PDO instance.
     *
     * @return PDO
     */
    public function getPDO()
    {
        return $this->pdo;
    }

    /**
     * Creates a SELECT query.
     *
     * @param string|array $fields select fields
     *
     * @return \JAQB\Query\SelectQuery
     */
    public function select($fields = '*')
    {
        $query = new Query\SelectQuery();

        return $query->setPDO($this->pdo)->select($fields);
    }

    /**
     * Creates an INSERT query.
     *
     * @param array $values insert values
     *
     * @return \JAQB\Query\InsertQuery
     */
    public function insert(array $values)
    {
        $query = new Query\InsertQuery();

        return $query->setPDO($this->pdo)->values($values);
    }

    /**
     * Creates an UPDATE query.
     *
     * @param string $table update table
     *
     * @return \JAQB\Query\UpdateQuery
     */
    public function update($table)
    {
        $query = new Query\UpdateQuery();

        return $query->setPDO($this->pdo)->table($table);
    }

    /**
     * Creates a DELETE query.
     *
     * @param string $from delete table
     *
     * @return \JAQB\Query\DeleteQuery
     */
    public function delete($from)
    {
        $query = new Query\DeleteQuery();

        return $query->setPDO($this->pdo)->from($from);
    }

    /**
     * Creates a raw SQL query.
     *
     * @param string $sql SQL statement
     *
     * @return \JAQB\Query\SqlQuery
     */
    public function raw($sql)
    {
        $query = new Query\SqlQuery();

        return $query->setPDO($this->pdo)->raw($sql);
    }
}
