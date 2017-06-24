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
            $this->where->addOrCondition($field, $condition, $operator);
        } else {
            $this->where->addOrCondition($field);
        }

        return $this;
    }

    /**
     * Sets the where conditions for the query with infix style arguments.
     *
     * @param array|string $field
     * @param string       $operator  operator (optional)
     * @param string|bool  $condition condition value (optional)
     *
     * @return self
     */
    public function whereInfix($field, $operator = '=', $condition = false)
    {
        $numArgs = func_num_args();
        if ($numArgs > 2) {
            $this->where->addCondition($field, $condition, $operator);
        } elseif ($numArgs == 2) {
            $this->where->addCondition($field, $operator, '=');
        } else {
            $this->where->addCondition($field);
        }

        return $this;
    }

    /**
     * Adds a where or condition to the query with infix style arguments.
     *
     * @param array|string $field
     * @param string       $operator  operator (optional)
     * @param string       $condition condition value (optional)
     *
     * @return self
     */
    public function orWhereInfix($field, $operator = '=', $condition = false)
    {
        $numArgs = func_num_args();
        if ($numArgs > 2) {
            $this->where->addOrCondition($field, $condition, $operator);
        } elseif ($numArgs == 2) {
            $this->where->addOrCondition($field, $operator, '=');
        } else {
            $this->where->addOrCondition($field);
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
     * @return \JAQB\Statement\WhereStatement
     */
    public function getWhere()
    {
        return $this->where;
    }
}
