<?php

/**
 * @author Jared King <j@jaredtking.com>
 *
 * @link http://jaredtking.com
 *
 * @copyright 2015 Jared King
 * @license MIT
 */
use JAQB\Query\SelectQuery;
use JAQB\Statement\UnionStatement;

class UnionStatementTest extends PHPUnit_Framework_TestCase
{
    public function testAddQuery()
    {
        $stmt = new UnionStatement();

        $query = new SelectQuery();
        $query2 = new SelectQuery();

        $this->assertEquals($stmt, $stmt->addQuery($query));
        $this->assertEquals($stmt, $stmt->addQuery($query2, 'ALL'));

        $this->assertEquals([[$query, false], [$query2, 'ALL']], $stmt->getQueries());
    }

    public function testBuild()
    {
        $stmt = new UnionStatement();
        $this->assertEquals('', $stmt->build());

        $query = new SelectQuery();
        $query->from('Users')
               ->where('username', 'john');

        $query2 = new SelectQuery();
        $query2->from('Users2');

        $stmt->addQuery($query)
             ->addQuery($query2, 'ALL');

        $this->assertEquals('UNION SELECT * FROM `Users` WHERE `username`=? UNION ALL SELECT * FROM `Users2`', $stmt->build());
        $this->assertEquals(['john'], $stmt->getValues());
    }
}
