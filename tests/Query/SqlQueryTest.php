<?php

/**
 * @package JAQB
 * @author Jared King <j@jaredtking.com>
 * @link http://jaredtking.com
 * @copyright 2015 Jared King
 * @license MIT
 */

use JAQB\Query\SqlQuery;

class SqlQueryTest extends \PHPUnit_Framework_TestCase
{
    public function testRaw()
    {
        $query = new SqlQuery();
        $this->assertEquals($query, $query->raw('SHOW COLUMNS FROM test'));
        $this->assertEquals('SHOW COLUMNS FROM test', $query->build());
    }

    public function testParameters()
    {
        $query = new SqlQuery();
        $this->assertEquals($query, $query->parameters(['test']));
        $this->assertEquals(['test'], $query->getValues());
    }
}
