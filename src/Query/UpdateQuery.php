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
use JAQB\Query\Traits\Limit;
use JAQB\Query\Traits\OrderBy;
use JAQB\Query\Traits\Where;

class UpdateQuery extends AbstractQuery
{
    use Executable, Limit, OrderBy, Where;

    /**
     * @var FromStatement
     */
    protected $table;

    /**
     * @var SetStatement
     */
    protected $set;

    public function __construct()
    {
        $this->table = new FromStatement(FromStatement::UPDATE);
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
     * Generates the raw SQL string for the query.
     *
     * @return string
     */
    public function build()
    {
        $sql = [
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
