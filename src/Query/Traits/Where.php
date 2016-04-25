<?php

namespace JAQB\Query\Traits;

trait Where
{
    /**
     * @var \JAQB\Statement\WhereStatement
     */
    protected $where;

    /**
     * Sets the where conditions for the query.
     *
     * @param array|string $field
     * @param string|bool  $condition condition value (optional)
     * @param string       $operator  operator (optional)
     *
     * @return self
     */
    public function where($field, $condition = false, $operator = '=')
    {
        if (func_num_args() >= 2) {
            $this->where->addCondition($field, $condition, $operator);
        } else {
            $this->where->addCondition($field);
        }

        return $this;
    }

    /**
     * Adds a where or condition to the query.
     *
     * @param array|string $field
     * @param string       $condition condition value (optional)
     * @param string       $operator  operator (optional)
     *
     * @return self
     */
    public function orWhere($field, $condition = false, $operator = '=')
    {
        if (func_num_args() >= 2) {
            $this->where->addConditionOr($field, $condition, $operator);
        } else {
            $this->where->addConditionOr($field);
        }

        return $this;
    }

    /**
     * Adds a where not condition to the query.
     *
     * @param string $field
     * @param string $condition condition value (optional)
     *
     * @return self
     */
    public function not($field, $condition = true)
    {
        $this->where->addCondition($field, $condition, '<>');

        return $this;
    }

    /**
     * Adds a where between condition to the query.
     *
     * @param string $field
     * @param mixed  $a     first between value
     * @param mixed  $b     second between value
     *
     * @return self
     */
    public function between($field, $a, $b)
    {
        $this->where->addBetweenCondition($field, $a, $b);

        return $this;
    }

    /**
     * Adds a where not between condition to the query.
     *
     * @param string $field
     * @param mixed  $a     first between value
     * @param mixed  $b     second between value
     *
     * @return self
     */
    public function notBetween($field, $a, $b)
    {
        $this->where->addNotBetweenCondition($field, $a, $b);

        return $this;
    }

    /**
     * Adds an exists condition to the query.
     *
     * @param callable $f
     *
     * @return self
     */
    public function exists(callable $f)
    {
        $this->where->addExistsCondition($f);

        return $this;
    }

    /**
     * Adds a not exists condition to the query.
     *
     * @param callable $f
     *
     * @return self
     */
    public function notExists(callable $f)
    {
        $this->where->addNotExistsCondition($f);

        return $this;
    }

    /**
     * Gets the where statement for the query.
     *
     * @return WhereStatement
     */
    public function getWhere()
    {
        return $this->where;
    }
}
