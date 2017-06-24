<?php

/**
 * @author Jared King <j@jaredtking.com>
 *
 * @see http://jaredtking.com
 *
 * @copyright 2015 Jared King
 * @license MIT
 */

namespace JAQB\Operations;

trait Executable
{
    /**
     * @var \PDO
     */
    protected $pdo;

    /**
     * @var int
     */
    protected $rowCount;

    /**
     * Sets the PDO instance used by this query.
     *
     * @param \PDO $pdo
     *
     * @return self
     */
    public function setPDO($pdo)
    {
        $this->pdo = $pdo;

        return $this;
    }

    /**
     * Gets the PDO instance used by this query.
     *
     * @return \PDO
     */
    public function getPDO()
    {
        return $this->pdo;
    }

    /**
     * Executes a query.
     *
     * @return \PDOStatement|bool result
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
