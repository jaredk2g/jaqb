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
use JAQB\Statement\SetStatement;
use JAQB\Statement\WhereStatement;

class UpdateQuery extends AbstractQuery
{
    use Executable;

    /**
     * @var FromStatement
     */
    protected $table;

    /**
     * @var SetStatement
     */
    protected $set;

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
        $this->table = new FromStatement(false);
        $this->set = new SetStatement();
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
    public function table($table)
    {
        $this->table->addTable($table);

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
     * Sets the values for the query.
     *
     * @param array $values
     *
     * @return self
     */
    public function values(array $values)
    {
        $this->set->addValues($values);

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
     * Gets the table name for the query.
     *
     * @return FromStatement
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Gets the values for the query.
     *
     * @return array
     */
    public function getSet()
    {
        return $this->set;
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
     * Gets the order by statement for the query.
     *
     * @return OrderByStatement
     */
    public function getOrderBy()
    {
        return $this->orderBy;
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
     * Generates the raw SQL string for the query.
     *
     * @return string
     */
    public function build()
    {
        $sql = [
            'UPDATE',
            $this->table->build(),
            $this->set->build(),
            $this->where->build(),
            $this->orderBy->build(),
            $this->limit->build(),
        ];

        $this->values = array_merge(
            array_values($this->set->getValues()),
            $this->where->getValues());

        return implode(' ', array_filter($sql));
    }

    public function __clone()
    {
        $this->table = clone $this->table;
        $this->set = clone $this->set;
        $this->where = clone $this->where;
        $this->orderBy = clone $this->orderBy;
        $this->limit = clone $this->limit;
    }
}
