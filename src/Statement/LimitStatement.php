<?php

/**
 * @author Jared King <j@jaredtking.com>
 *
 * @see http://jaredtking.com
 *
 * @copyright 2015 Jared King
 * @license MIT
 */

namespace JAQB\Statement;

class LimitStatement extends Statement
{
    /**
     * @var int
     */
    protected $limit;

    /**
     * @var int
     */
    protected $offset = 0;

    /**
     * @var int
     */
    protected $max;

    /**
     * @param int $max maximum # the limit can be set to, 0=infinity
     */
    public function __construct($max = 0)
    {
        $this->max = $max;
    }

    /**
     * Sets the limit and start offset.
     *
     * @param int $limit
     * @param int $offset
     *
     * @return self
     */
    public function setLimit($limit, $offset = 0)
    {
        if (is_numeric($limit) && is_numeric($offset)) {
            $this->limit = (int) $limit;
            $this->offset = (int) $offset;
        }

        return $this;
    }

    /**
     * Gets the start offset.
     *
     * @return int
     */
    public function getStart()
    {
        return $this->offset;
    }

    /**
     * Gets the limit.
     *
     * @return int
     */
    public function getLimit()
    {
        if ($this->max > 0) {
            return min($this->max, $this->limit);
        }

        return $this->limit;
    }

    public function build()
    {
        if (!$this->limit) {
            return '';
        }

        if (!$this->offset) {
            return 'LIMIT '.$this->limit;
        }

        return 'LIMIT '.(string) $this->offset.','.$this->limit;
    }
}
