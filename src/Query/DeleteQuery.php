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

use JAQB\Operations\Executable;
use JAQB\Statement\FromStatement;
use JAQB\Statement\LimitStatement;
use JAQB\Statement\OrderStatement;
use JAQB\Statement\WhereStatement;

class DeleteQuery extends AbstractQuery
{
    use Executable;

    /**
     * @var FromStatement
     */
    protected $from;

    /**
     * @var WhereStatement
     */
    protected $where;

    /**
     * @var OrderStatement
     */
    protected $orderBy;

    /**
     * @var LimitStatement
     */
    protected $limit;

    public function __construct()
    {
        $this->from = new FromStatement();
        $this->where = new WhereStatement();
        $this->orderBy = new OrderStatement();
        $this->limit = new LimitStatement();
    }

    /**
     * Sets the table for the query.
     *
     * @param string $table table name
     *
     * @return self
     */
    public function from($table)
    {
        $this->from->addTable($table);

        return $this;
    }

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
     * Sets the limit for the query.
     *
     * @param int $limit
     * @param int $offset
     *
     * @return self
     */
    public function limit($limit, $offset = 0)
    {
        $this->limit->setLimit($limit, $offset);

        return $this;
    }

    /**
     * Sets the order for the query.
     *
     * @param string|array $fields
     * @param string       $direction
     *
     * @return self
     */
    public function orderBy($fields, $direction = false)
    {
        $this->orderBy->addFields($fields, $direction);

        return $this;
    }

    /**
     * Gets the from statement for the query.
     *
     * @return FromStatement
     */
    public function getFrom()
    {
        return $this->from;
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

    /**
     * Gets the limit statement for the query.
     *
     * @return LimitStatement
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Gets the order by statement for the query.
     *
     * @return OrderByStatement
     */
    public function getOrderBy()
    {
        return $this->orderBy;
    }

    /**
     * Generates the raw SQL string for the query.
     *
     * @return string
     */
    public function build()
    {
        $sql = [
            'DELETE',
            $this->from->build(),
            $this->where->build(),
            $this->orderBy->build(),
            $this->limit->build(),
        ];

        $this->values = $this->where->getValues();

        return implode(' ', array_filter($sql));
    }

    public function __clone()
    {
        $this->from = clone $this->from;
        $this->where = clone $this->where;
        $this->orderBy = clone $this->orderBy;
        $this->limit = clone $this->limit;
    }
}
