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
use JAQB\Operations\Fetchable;
use JAQB\Statement\SelectStatement;
use JAQB\Statement\FromStatement;
use JAQB\Statement\WhereStatement;
use JAQB\Statement\OrderStatement;
use JAQB\Statement\LimitStatement;
use JAQB\Statement\UnionStatement;

class SelectQuery extends AbstractQuery
{
    use Executable, Fetchable;

    /**
     * @var SelectStatement
     */
    protected $select;

    /**
     * @var FromStatement
     */
    protected $from;

    /**
     * @var WhereStatement
     */
    protected $where;

    /**
     * @var WhereStatement
     */
    protected $having;

    /**
     * @var OrderStatement
     */
    protected $orderBy;

    /**
     * @var OrderStatement
     */
    protected $groupBy;

    /**
     * @var LimitStatement
     */
    protected $limit;

    /**
     * @var UnionStatement
     */
    protected $union;

    public function __construct()
    {
        $this->select = new SelectStatement();
        $this->from = new FromStatement();
        $this->where = new WhereStatement();
        $this->having = new WhereStatement(true);
        $this->orderBy = new OrderStatement();
        $this->groupBy = new OrderStatement(true);
        $this->limit = new LimitStatement();
        $this->union = new UnionStatement();
    }

    /**
     * Sets the fields to be selected for the query.
     *
     * @param array|string $fields fields
     *
     * @return self
     */
    public function select($fields)
    {
        $this->select->addFields($fields);

        return $this;
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
     * Adds a join to the query.
     *
     * @param string $table table name
     * @param string $on    ON condition
     * @param string $using USING columns
     * @param string $type  optional join type if not JOIN
     *
     * @return self
     */
    public function join($table, $on = null, $using = null, $type = 'JOIN')
    {
        $this->from->addJoin($table, $on, $using, $type);

        return $this;
    }

    /**
     * Adds a where condition to the query.
     *
     * @param array|string $field
     * @param string       $condition condition value (optional)
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
     * Sets the group by fields for the query.
     *
     * @param string|array $fields
     * @param string       $direction
     *
     * @return self
     */
    public function groupBy($fields, $direction = false)
    {
        $this->groupBy->addFields($fields, $direction);

        return $this;
    }

    /**
     * Sets the having conditions for the query.
     *
     * @param array|string $field
     * @param string|bool  $condition condition value (optional)
     * @param string       $operator  operator (optional)
     *
     * @return self
     */
    public function having($field, $condition = false, $operator = '=')
    {
        if (func_num_args() >= 2) {
            $this->having->addCondition($field, $condition, $operator);
        } else {
            $this->having->addCondition($field);
        }

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
     * Unions another select query with this query.
     *
     * @param SelectQuery $query
     * @param string      $type  optional union type
     *
     * @return self
     */
    public function union(SelectQuery $query, $type = false)
    {
        $this->union->addQuery($query, $type);

        return $this;
    }

    /**
     * Gets the select statement for the query.
     *
     * @return SelectStatement
     */
    public function getSelect()
    {
        return $this->select;
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
     * Gets the group by statement for the query.
     *
     * @return GroupByStatement
     */
    public function getGroupBy()
    {
        return $this->groupBy;
    }

    /**
     * Gets the having statement for the query.
     *
     * @return HavingStatement
     */
    public function getHaving()
    {
        return $this->having;
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
     * Gets the union statement for the query.
     *
     * @return UnionStatement
     */
    public function getUnion()
    {
        return $this->union;
    }

    /**
     * Generates the raw SQL string for the query.
     *
     * @return string
     */
    public function build()
    {
        $sql = [
            $this->select->build(),
            $this->from->build(),
            $this->where->build(),
            $this->groupBy->build(),
            $this->having->build(),
            $this->orderBy->build(),
            $this->limit->build(),
            $this->union->build(),
        ];

        $this->values = array_merge(
            $this->where->getValues(),
            $this->having->getValues(),
            $this->union->getValues());

        return implode(' ', array_filter($sql));
    }

    public function __clone()
    {
        $this->select = clone $this->select;
        $this->from = clone $this->from;
        $this->where = clone $this->where;
        $this->groupBy = clone $this->groupBy;
        $this->having = clone $this->having;
        $this->orderBy = clone $this->orderBy;
        $this->limit = clone $this->limit;
        $this->union = clone $this->union;
    }
}
