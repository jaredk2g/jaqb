<?php

namespace JAQB\Query\Traits;

trait From
{
    /**
     * @var \JAQB\Statement\FromStatement
     */
    protected $from;

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
     * @return \JAQB\Statement\FromStatement
     */
    public function getFrom()
    {
        return $this->from;
    }
}
