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

class ValuesStatement extends Statement
{
    /**
     * @var array
     */
    protected $insertRows = [];

    /**
     * Adds values to the statement.
     *
     * @return self
     */
    public function addValues(array $values)
    {
        // Check if this is a multi-dimensional array
        $isMultiDimensional = 0 == count($values) || (isset($values[0]) && is_array($values[0]));

        if ($isMultiDimensional) {
            $this->insertRows = array_merge($this->insertRows, $values);
        } else {
            // If a single row is provided then it is added
            // to the last row, or a new row if there are none.
            // This is done to maintain BC
            if (count($this->insertRows) > 0) {
                $k = count($this->insertRows) - 1;
                $this->insertRows[$k] = array_replace($this->insertRows[$k], $values);
            } else {
                $this->insertRows = [$values];
            }
        }

        return $this;
    }

    /**
     * @deprecated
     * Gets the values being inserted (first row only).
     *
     * WARNING: this only returns the first row for BC
     *
     * @return array
     */
    public function getInsertValues()
    {
        return count($this->insertRows) > 0 ? $this->insertRows[0] : [];
    }

    /**
     * Gets the rows being inserted.
     *
     * @return array
     */
    public function getInsertRows()
    {
        return $this->insertRows;
    }

    public function build()
    {
        // reset the parameterized values
        $this->values = [];

        // get the list of fields from the first row
        $keys = [];
        $fields = [];
        if (count($this->insertRows) > 0) {
            foreach (array_keys($this->insertRows[0]) as $key) {
                if ($id = $this->escapeIdentifier($key)) {
                    $fields[] = $id;
                    $keys[] = $key;
                }
            }
        }

        if (0 == count($fields)) {
            return '';
        }

        foreach ($this->insertRows as $row) {
            foreach ($keys as $key) {
                $this->values[] = isset($row[$key]) ? $row[$key] : null;
            }
        }

        $rowPlaceholders = '('.implode(', ', array_fill(0, count($fields), '?')).')';

        // produces "(`col1`,`col2`,`col3`) VALUES (?,?,?), (?,?,?)"
        return '('.implode(', ', $fields).') VALUES '
            .implode(', ', array_fill(0, count($this->insertRows), $rowPlaceholders));
    }
}
