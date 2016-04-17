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

class SetStatement extends Statement
{
    /**
     * @var array
     */
    protected $setValues = [];

    /**
     * Adds values to the statement.
     *
     * @return self
     */
    public function addValues(array $values)
    {
        $this->setValues = array_replace($this->setValues, $values);

        return $this;
    }

    /**
     * Gets the values being set.
     *
     * @return array
     */
    public function getSetValues()
    {
        return $this->setValues;
    }

    public function build()
    {
        // reset the parameterized values
        $this->values = [];

        $fields = [];
        foreach ($this->setValues as $key => $value) {
            if ($id = $this->escapeIdentifier($key)) {
                $fields[] = $id.'=?';
                $this->values[] = $value;
            }
        }

        if (count($fields) == 0) {
            return '';
        }

        // generates SET `col1`=?,`col2`=?,`col3`=?
        return 'SET '.implode(',', $fields);
    }
}
