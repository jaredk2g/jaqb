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

abstract class AbstractQuery
{
    /**
     * @var array
     */
    protected $values = [];

    /**
     * Builds a SQL string for the query.
     *
     * @return string SQL
     */
    abstract public function build();

    /**
     * Gets the parameterized values associated with this query.
     *
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }
}
