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

class SelectQueryTest extends PHPUnit_Framework_TestCase
{
    public function testSelect()
    {
        $query = new SelectQuery();

        $this->assertEquals($query, $query->select('name'));
        $this->assertInstanceOf('JAQB\Statement\SelectStatement', $query->getSelect());
        $this->assertEquals(['name'], $query->getSelect()->getFields());
    }

    public function testFrom()
    {
        $query = new SelectQuery();

        $this->assertEquals($query, $query->from('Users'));
        $this->assertInstanceOf('JAQB\Statement\FromStatement', $query->getFrom());
        $this->assertEquals(['Users'], $query->getFrom()->getTables());
    }

    public function testJoin()
    {
        $query = new SelectQuery();

        $this->assertEquals($query, $query->join('t2'));
        $this->assertInstanceOf('JAQB\Statement\FromStatement', $query->getFrom());
        $this->assertEquals([['JOIN', ['t2'], null, []]], $query->getFrom()->getJoins());
    }

    public function testWhere()
    {
        $query = new SelectQuery();

        $this->assertEquals($query, $query->where('balance', 10, '>'));
        $this->assertEquals($query, $query->where('notes IS NULL'));
        $where = $query->getWhere();
        $this->assertInstanceOf('JAQB\Statement\WhereStatement', $where);
        $this->assertFalse($where->isHaving());
        $this->assertEquals([['balance', '>', 10], ['notes IS NULL']], $where->getConditions());
    }

    public function testBetween()
    {
        $query = new SelectQuery();

        $this->assertEquals($query, $query->between('date', 2015, 2016));
        $this->assertEquals([['date', 'BETWEEN', 2015, 2016]], $query->getWhere()->getConditions());
    }

    public function testLimit()
    {
        $query = new SelectQuery();

        $this->assertEquals($query, $query->limit(10));
        $limit = $query->getLimit();
        $this->assertInstanceOf('JAQB\Statement\LimitStatement', $limit);
        $this->assertEquals(10, $limit->getLimit());
        $this->assertEquals(0, $limit->getStart());

        $this->assertEquals($query, $query->limit(100, 200));
        $this->assertEquals(100, $query->getLimit()->getLimit());
        $this->assertEquals(200, $query->getLimit()->getStart());

        $this->assertEquals($query, $query->limit('hello'));
        $this->assertEquals(100, $query->getLimit()->getLimit());
        $this->assertEquals(200, $query->getLimit()->getStart());
    }

    public function testGroupBy()
    {
        $query = new SelectQuery();

        $this->assertEquals($query, $query->groupBy('uid'));
        $groupBy = $query->getGroupBy();
        $this->assertInstanceOf('JAQB\Statement\OrderStatement', $groupBy);
        $this->assertTrue($groupBy->isGroupBy());
        $this->assertEquals([['uid']], $groupBy->getFields());
    }

    public function testHaving()
    {
        $query = new SelectQuery();

        $this->assertEquals($query, $query->having('balance', 10, '>'));
        $this->assertEquals($query, $query->having('notes IS NULL'));
        $having = $query->getHaving();
        $this->assertInstanceOf('JAQB\Statement\WhereStatement', $having);
        $this->assertTrue($having->isHaving());
        $this->assertEquals([['balance', '>', 10], ['notes IS NULL']], $having->getConditions());
    }

    public function testOrderBy()
    {
        $query = new SelectQuery();

        $this->assertEquals($query, $query->orderBy('uid', 'ASC'));
        $orderBy = $query->getOrderBy();
        $this->assertInstanceOf('JAQB\Statement\OrderStatement', $orderBy);
        $this->assertFalse($orderBy->isGroupBy());
        $this->assertEquals([['uid', 'ASC']], $orderBy->getFields());
    }

    public function testUnion()
    {
        $query = new SelectQuery();

        $query2 = new SelectQuery();
        $this->assertEquals($query, $query->union($query2));

        $query3 = new SelectQuery();
        $this->assertEquals($query, $query->union($query3, 'ALL'));

        $union = $query->getUnion();
        $this->assertInstanceOf('JAQB\Statement\UnionStatement', $union);
        $this->assertEquals([[$query2, false], [$query3, 'ALL']], $union->getQueries());
    }

    public function testBuild()
    {
        $query = new SelectQuery();

        $query2 = new SelectQuery();
        $query2->from('Users2')
               ->where('username', 'john');

        $query->from('Users')
              ->join('FbProfiles fb', 'uid = fb.uid')
              ->where('uid', 10)
              ->between('created_at', '2016-04-01', '2016-04-30')
              ->having('first_name', 'something')
              ->groupBy('last_name')
              ->orderBy('first_name', 'ASC')
              ->limit(100, 10)
              ->union($query2);

        // test for idempotence
        for ($i = 0; $i < 3; ++$i) {
            $this->assertEquals('SELECT * FROM `Users` JOIN `FbProfiles` `fb` ON uid = fb.uid WHERE `uid` = ? AND `created_at` BETWEEN ? AND ? GROUP BY `last_name` HAVING `first_name` = ? ORDER BY `first_name` ASC LIMIT 10,100 UNION SELECT * FROM `Users2` WHERE `username` = ?', $query->build());

            // test values
            $this->assertEquals([10, '2016-04-01', '2016-04-30', 'something', 'john'], $query->getValues());
        }
    }

    public function testClone()
    {
        $query = new SelectQuery();
        $query2 = clone $query;
        $this->assertNotSame($query->getSelect(), $query2->getSelect());
        $this->assertNotSame($query->getFrom(), $query2->getFrom());
        $this->assertNotSame($query->getWhere(), $query2->getWhere());
        $this->assertNotSame($query->getGroupBy(), $query2->getGroupBy());
        $this->assertNotSame($query->getHaving(), $query2->getHaving());
        $this->assertNotSame($query->getOrderBy(), $query2->getOrderBy());
        $this->assertNotSame($query->getLimit(), $query2->getLimit());
        $this->assertNotSame($query->getUnion(), $query2->getUnion());
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
        $pdo->shouldReceive('prepare')->withArgs(['SELECT * FROM `Test` WHERE `id` = ?'])
            ->andReturn($stmt);

        $query = new SelectQuery();
        $query->setPDO($pdo);
        $this->assertEquals($pdo, $query->getPDO());
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

        $query = new SelectQuery();
        $query->setPDO($pdo);

        $this->assertFalse($query->execute());
    }

    public function testOne()
    {
        $stmt = Mockery::mock();
        $stmt->shouldReceive('execute')->andReturn(true);
        $stmt->shouldReceive('rowCount')->andReturn(10);
        $stmt->shouldReceive('fetch')->withArgs([PDO::FETCH_ASSOC])
             ->andReturn(['field' => 'value']);

        $pdo = Mockery::mock();
        $pdo->shouldReceive('prepare')->withArgs(['SELECT * FROM `Test` WHERE `id` = ?'])
            ->andReturn($stmt);

        $query = new SelectQuery();
        $query->setPDO($pdo);
        $query->from('Test')->where('id', 'test');

        $this->assertEquals(['field' => 'value'], $query->one());
        $this->assertEquals(10, $query->rowCount());
    }

    public function testOneFail()
    {
        $stmt = Mockery::mock();
        $stmt->shouldReceive('execute')->andReturn(false);

        $pdo = Mockery::mock();
        $pdo->shouldReceive('prepare')->andReturn($stmt);

        $query = new SelectQuery();
        $query->setPDO($pdo);

        $this->assertFalse($query->one());
    }

    public function testAll()
    {
        $stmt = Mockery::mock();
        $stmt->shouldReceive('execute')->andReturn(true);
        $stmt->shouldReceive('rowCount')->andReturn(10);
        $stmt->shouldReceive('fetchAll')->withArgs([PDO::FETCH_ASSOC])
             ->andReturn([['field' => 'value'], ['field' => 'value2']]);

        $pdo = Mockery::mock();
        $pdo->shouldReceive('prepare')->withArgs(['SELECT * FROM `Test` WHERE `id` = ?'])
            ->andReturn($stmt);

        $query = new SelectQuery();
        $query->setPDO($pdo);
        $query->from('Test')->where('id', 'test');

        $this->assertEquals([['field' => 'value'], ['field' => 'value2']], $query->all());
        $this->assertEquals(10, $query->rowCount());
    }

    public function testAllFail()
    {
        $stmt = Mockery::mock();
        $stmt->shouldReceive('execute')->andReturn(false);

        $pdo = Mockery::mock();
        $pdo->shouldReceive('prepare')->andReturn($stmt);

        $query = new SelectQuery();
        $query->setPDO($pdo);

        $this->assertFalse($query->all());
    }

    public function testColumn()
    {
        $stmt = Mockery::mock();
        $stmt->shouldReceive('execute')->andReturn(true);
        $stmt->shouldReceive('rowCount')->andReturn(10);
        $stmt->shouldReceive('fetchAll')->withArgs([PDO::FETCH_COLUMN, 0])
             ->andReturn(['value', 'value2']);

        $pdo = Mockery::mock();
        $pdo->shouldReceive('prepare')->withArgs(['SELECT * FROM `Test` WHERE `id` = ?'])
            ->andReturn($stmt);

        $query = new SelectQuery();
        $query->setPDO($pdo);
        $query->from('Test')->where('id', 'test');

        $this->assertEquals(['value', 'value2'], $query->column());
        $this->assertEquals(10, $query->rowCount());
    }

    public function testColumnFail()
    {
        $stmt = Mockery::mock();
        $stmt->shouldReceive('execute')->andReturn(false);

        $pdo = Mockery::mock();
        $pdo->shouldReceive('prepare')->andReturn($stmt);

        $query = new SelectQuery();
        $query->setPDO($pdo);

        $this->assertFalse($query->column());
    }

    public function testScalar()
    {
        $stmt = Mockery::mock();
        $stmt->shouldReceive('execute')->andReturn(true);
        $stmt->shouldReceive('rowCount')->andReturn(10);
        $stmt->shouldReceive('fetchColumn')->withArgs([0])
             ->andReturn('scalar');

        $pdo = Mockery::mock();
        $pdo->shouldReceive('prepare')->withArgs(['SELECT * FROM `Test` WHERE `id` = ?'])
            ->andReturn($stmt);

        $query = new SelectQuery();
        $query->setPDO($pdo);
        $query->from('Test')->where('id', 'test');

        $this->assertEquals('scalar', $query->scalar());
        $this->assertEquals(10, $query->rowCount());
    }

    public function testScalarFail()
    {
        $stmt = Mockery::mock();
        $stmt->shouldReceive('execute')->andReturn(false);

        $pdo = Mockery::mock();
        $pdo->shouldReceive('prepare')->andReturn($stmt);

        $query = new SelectQuery();
        $query->setPDO($pdo);

        $this->assertFalse($query->scalar());
    }
}
