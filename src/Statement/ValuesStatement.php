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

class ValuesStatement extends Statement
{
    /**
     * @var array
     */
    protected $insertValues = [];

    /**
     * Adds values to the statement.
     *
     * @return self
     */
    public function addValues(array $values)
    {
        $this->insertValues = array_replace($this->insertValues, $values);

        return $this;
    }

    /**
     * Gets the values being inserted.
     *
     * @return array
     */
    public function getInsertValues()
    {
        return $this->insertValues;
    }

    public function build()
    {
        // reset the parameterized values
        $this->values = [];

        $fields = [];
        foreach ($this->insertValues as $key => $value) {
            if ($id = $this->escapeIdentifier($key)) {
                $fields[] = $id;
                $this->values[] = $value;
            }
        }

        if (count($fields) == 0) {
            return '';
        }

        // generates (`col1`,`col2`,`col3`) VALUES (?,?,?)
        return '('.implode(', ', $fields).') VALUES ('.
            implode(', ', array_fill(0, count($fields), '?')).')';
    }
}
