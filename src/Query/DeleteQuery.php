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
use JAQB\Query\Traits\Limit;
use JAQB\Query\Traits\OrderBy;
use JAQB\Query\Traits\Where;

class DeleteQuery extends AbstractQuery
{
    use Executable, Limit, OrderBy, Where;

    /**
     * @var FromStatement
     */
    protected $from;

    public function __construct()
    {
        $this->from = new FromStatement(FromStatement::DELETE);
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
     * Gets the from statement for the query.
     *
     * @return FromStatement
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Generates the raw SQL string for the query.
     *
     * @return string
     */
    public function build()
    {
        $sql = [
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
