<?php

/**
 * @author Jared King <j@jaredtking.com>
 *
 * @link http://jaredtking.com
 *
 * @copyright 2015 Jared King
 * @license MIT
 */
use JAQB\Query\DeleteQuery;

class DeleteQueryTest extends \PHPUnit_Framework_TestCase
{
    public function testFrom()
    {
        $query = new DeleteQuery();

        $this->assertEquals($query, $query->from('Users'));
        $this->assertInstanceOf('\\JAQB\\Statement\\FromStatement', $query->getFrom());
        $this->assertEquals(['Users'], $query->getFrom()->getTables());
    }

    public function testWhere()
    {
        $query = new DeleteQuery();

        $this->assertEquals($query, $query->where('balance', 10, '>'));
        $this->assertEquals($query, $query->where('notes IS NULL'));
        $where = $query->getWhere();
        $this->assertInstanceOf('\\JAQB\\Statement\\WhereStatement', $where);
        $this->assertFalse($where->isHaving());
        $this->assertEquals([['balance', '>', 10], ['notes IS NULL']], $where->getConditions());
    }

    public function testOrderBy()
    {
        $query = new DeleteQuery();

        $this->assertEquals($query, $query->orderBy('uid', 'ASC'));
        $orderBy = $query->getOrderBy();
        $this->assertInstanceOf('\\JAQB\\Statement\\OrderStatement', $orderBy);
        $this->assertFalse($orderBy->isGroupBy());
        $this->assertEquals([['uid', 'ASC']], $orderBy->getFields());
    }

    public function testLimit()
    {
        $query = new DeleteQuery();

        $this->assertEquals($query, $query->limit(10));
        $limit = $query->getLimit();
        $this->assertInstanceOf('\\JAQB\\Statement\\LimitStatement', $limit);
        $this->assertEquals(10, $limit->getLimit());

        $this->assertEquals($query, $query->limit('hello'));
        $this->assertEquals(10, $query->getLimit()->getLimit());
    }

    public function testBuild()
    {
        $query = new DeleteQuery();

        $query->from('Users')->where('uid', 10)->limit(100)->orderBy('uid', 'ASC');

        $this->assertEquals('DELETE FROM `Users` WHERE `uid`=? ORDER BY `uid` ASC LIMIT 100', $query->build());

        // test values
        $this->assertEquals([10], $query->getValues());
    }

    ////////////////////////
    // Operations
    ////////////////////////

    public function testExecute()
    {
        $stmt = Mockery::mock();
        $stmt->shouldReceive('execute')->andReturn(true);
        $stmt->shouldReceive('rowCount')->andReturn(10);

        $pdo = Mockery::mock();
        $pdo->shouldReceive('prepare')->withArgs(['DELETE FROM `Test` WHERE `id`=?'])
            ->andReturn($stmt);

        $query = new DeleteQuery($pdo);
        $query->from('Test')->where('id', 'test');

        $this->assertEquals($stmt, $query->execute());
        $this->assertEquals(10, $query->rowCount());
    }

    public function testExecuteFail()
    {
        $stmt = Mockery::mock();
        $stmt->shouldReceive('execute')->andReturn(false);

        $pdo = Mockery::mock();
        $pdo->shouldReceive('prepare')->andReturn($stmt);

        $query = new DeleteQuery($pdo);

        $this->assertFalse($query->execute());
    }
}
