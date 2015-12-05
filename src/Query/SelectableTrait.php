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

trait SelectableTrait
{
    /**
     * Executes a query and returns the first row.
     *
     * @param int $style PDO fetch style
     *
     * @return mixed|bool result
     */
    public function one($style = PDO::FETCH_ASSOC)
    {
        $stmt = $this->execute();

        if ($stmt) {
            return $stmt->fetch($style);
        } else {
            return false;
        }
    }

    /**
     * Executes a query and returns all of the rows.
     *
     * @param int $style PDO fetch style
     *
     * @return mixed|bool result
     */
    public function all($style = PDO::FETCH_ASSOC)
    {
        $stmt = $this->execute();

        if ($stmt) {
            return $stmt->fetchAll($style);
        } else {
            return false;
        }
    }

    /**
     * Executes a query and returns a column from all rows.
     *
     * @param int $index zero-indexed column to fetch
     *
     * @return mixed|bool result
     */
    public function column($index = 0)
    {
        $stmt = $this->execute();

        if ($stmt) {
            return $stmt->fetchAll(PDO::FETCH_COLUMN, $index);
        } else {
            return false;
        }
    }

    /**
     * Executes a query and returns a value from the first row.
     *
     * @param int $index zero-indexed column to fetch
     *
     * @return mixed|bool result
     */
    public function scalar($index = 0)
    {
        $stmt = $this->execute();

        if ($stmt) {
            return $stmt->fetchColumn($index);
        } else {
            return false;
        }
    }
}
