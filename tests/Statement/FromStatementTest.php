<?php

/**
 * @author Jared King <j@jaredtking.com>
 *
 * @link http://jaredtking.com
 *
 * @copyright 2015 Jared King
 * @license MIT
 */
use JAQB\Statement\FromStatement;

class FromStatementTest extends PHPUnit_Framework_TestCase
{
    public function testAddTable()
    {
        $stmt = new FromStatement();
        $this->assertEquals($stmt, $stmt->addTable(['test', 'test2']));
        $this->assertEquals(['test', 'test2'], $stmt->getTables());

        $this->assertEquals($stmt, $stmt->addTable(['test3']));
        $this->assertEquals(['test', 'test2', 'test3'], $stmt->getTables());
    }

    public function testAddTableString()
    {
        $stmt = new FromStatement();
        $this->assertEquals($stmt, $stmt->addTable('test'));
        $this->assertEquals(['test'], $stmt->getTables());

        $stmt = new FromStatement();
        $this->assertEquals($stmt, $stmt->addTable('test, test2'));
        $this->assertEquals(['test', 'test2'], $stmt->getTables());
    }

    public function testJoin()
    {
        $stmt = new FromStatement();
        $this->assertEquals($stmt, $stmt->addJoin(['table1 t1', 'table2 t2'], 'id=t1.id', 'c1,c2'));
        $this->assertEquals([['JOIN', ['table1 t1', 'table2 t2'], 'id=t1.id', ['c1', 'c2']]], $stmt->getJoins());
    }

    public function testJoinString()
    {
        $stmt = new FromStatement();
        $this->assertEquals($stmt, $stmt->addJoin('table1 t1,table2 t2', 'id=t1.id', 'c1,c2', 'LEFT INNER JOIN'));
        $this->assertEquals([['LEFT INNER JOIN', ['table1 t1', 'table2 t2'], 'id=t1.id', ['c1', 'c2']]], $stmt->getJoins());
    }

    public function testBuild()
    {
        $stmt = new FromStatement();
        $this->assertEquals($stmt, $stmt->addTable('test,test2,should"_not===_work'));
        $this->assertEquals('FROM `test`,`test2`', $stmt->build());

        $this->assertEquals($stmt, $stmt->addJoin('table1 t1,table2 t2', 'test.id=t1.id', 'c1,c2'));
        $this->assertEquals('FROM `test`,`test2` JOIN `table1` `t1`, `table2` `t2` ON test.id=t1.id USING (`c1`, `c2`)', $stmt->build());

        $stmt = new FromStatement();
        $stmt->addTable('t1');
        $stmt->addJoin('t2');
        $this->assertEquals('FROM `t1` JOIN `t2`', $stmt->build());

        $stmt = new FromStatement();
        $this->assertEquals('', $stmt->build());
    }
}
