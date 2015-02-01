<?php

/**
 * @package JAQB
 * @author Jared King <j@jaredtking.com>
 * @link http://jaredtking.com
 * @copyright 2015 Jared King
 * @license MIT
 */

namespace JAQB;

class QueryBuilder
{
    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * @param PDO $pdo
     */
    public function __construct($pdo = null)
    {
        $this->pdo = $pdo;
    }

    /**
     * Returns the PDO instance
     *
     * @return PDO
     */
    public function getPDO()
    {
        return $this->pdo;
    }

    /**
     * Creates a SELECT query
     *
     * @param string|array $fields select fields
     *
     * @return SelectQuery
     */
    public function select($fields = '*')
    {
        $query = new Query\SelectQuery($this->pdo);

        return $query->select($fields);
    }

    /**
     * Creates an INSERT query
     *
     * @param array $values insert values
     *
     * @return InsertQuery
     */
    public function insert(array $values)
    {
        $query = new Query\InsertQuery($this->pdo);

        return $query->values($values);
    }

    /**
     * Creates an UPDATE query
     *
     * @param string|array $table update table
     *
     * @return UpdateQuery
     */
    public function update($table)
    {
        $query = new Query\UpdateQuery($this->pdo);

        return $query->table($table);
    }

    /**
     * Creates a DELETE query
     *
     * @param string $from delete table
     *
     * @return DeleteQuery
     */
    public function delete($from)
    {
        $query = new Query\DeleteQuery($this->pdo);

        return $query->from($from);
    }

    /**
     * Creates a raw SQL query
     *
     * @param string $sql SQL statement
     *
     * @return SqlQuery
     */
    public function raw($sql)
    {
        $query = new Query\SqlQuery($this->pdo);

        return $query->raw($sql);
    }
}
