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

use JAQB\Statement\FromStatement;
use JAQB\Statement\WhereStatement;
use JAQB\Statement\OrderStatement;

class DeleteQuery extends Query
{
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
     * @var string
     */
    protected $limit;

    public function initialize()
    {
        $this->from = new FromStatement();
        $this->where = new WhereStatement();
        $this->orderBy = new OrderStatement();
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
     * Sets the limit for the query.
     *
     * @param int $limit
     *
     * @return self
     */
    public function limit($limit)
    {
        if (is_numeric($limit)) {
            $this->limit = (string) $limit;
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
     * Gets the limit for the query.
     *
     * @return string limit
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
            $this->from->build(), ]; // from

        $this->values = [];

        // where
        $where = $this->where->build();
        if (!empty($where)) {
            $sql[] = $where;
            $this->values = array_merge($this->values, $this->where->getValues());
        }

        // order by
        $orderBy = $this->orderBy->build();
        if (!empty($orderBy)) {
            $sql[] = $orderBy;
        }

        // limit
        if ($this->limit) {
            $sql[] = 'LIMIT '.$this->limit;
        }

        return implode(' ', $sql);
    }
}
