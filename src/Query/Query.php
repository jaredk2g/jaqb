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

use PDO;

abstract class Query
{
    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * @var array
     */
    protected $values = [];

    /**
     * @var int
     */
    protected $rowCount;

    /**
     * @param PDO $pdo
     */
    public function __construct($pdo = null)
    {
        $this->pdo = $pdo;
        $this->initialize();
    }

    /**
     * Initializes the query.
     */
    abstract public function initialize();

    /**
     * Builds a SQL string for the query.
     *
     * @return string SQL
     */
    abstract public function build();

    /**
     * Gets the values associated with this query.
     *
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * Executes a query.
     *
     * @return PDOStatement|bool result
     */
    public function execute()
    {
        $stmt = $this->pdo->prepare($this->build());

        if ($stmt->execute($this->getValues())) {
            $this->rowCount = $stmt->rowCount();

            return $stmt;
        } else {
            return false;
        }
    }

    /**
     * Returns the number of rows affected by the last executed statement.
     *
     * @return int
     */
    public function rowCount()
    {
        return $this->rowCount;
    }
}
