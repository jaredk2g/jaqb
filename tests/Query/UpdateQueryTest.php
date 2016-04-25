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
use JAQB\Query\UpdateQuery;

class UpdateQueryTest extends PHPUnit_Framework_TestCase
{
    public function testTable()
    {
        $query = new UpdateQuery();

        $this->assertEquals($query, $query->table('Users'));
        $this->assertInstanceOf('JAQB\Statement\FromStatement', $query->getTable());
        $this->assertEquals(['Users'], $query->getTable()->getTables());
    }

    public function testValues()
    {
        $query = new UpdateQuery();

        $this->assertEquals($query, $query->values(['test1' => 1, 'test2' => 2]));
        $this->assertEquals($query, $query->values(['test3' => 3]));
        $this->assertInstanceOf('JAQB\Statement\SetStatement', $query->getSet());
        $this->assertEquals(['test1' => 1, 'test2' => 2, 'test3' => 3], $query->getSet()->getSetValues());
    }

    public function testWhere()
    {
        $query = new UpdateQuery();

        $this->assertEquals($query, $query->where('balance', 10, '>'));
        $this->assertEquals($query, $query->where('notes IS NULL'));
        $where = $query->getWhere();
        $this->assertInstanceOf('JAQB\Statement\WhereStatement', $where);
        $this->assertFalse($where->isHaving());
        $this->assertEquals([['balance', '>', 10], ['notes IS NULL']], $where->getConditions());
    }

    public function testOrWhere()
    {
        $query = new UpdateQuery();

        $this->assertEquals($query, $query->orWhere('balance', 10, '>'));
        $this->assertEquals($query, $query->orWhere('notes IS NULL'));
        $where = $query->getWhere();
        $this->assertInstanceOf('JAQB\Statement\WhereStatement', $where);
        $this->assertFalse($where->isHaving());
        $this->assertEquals([['OR'], ['balance', '>', 10], ['OR'], ['notes IS NULL']], $where->getConditions());
    }

    public function testNot()
    {
        $query = new UpdateQuery();

        $this->assertEquals($query, $query->not('disabled'));
        $this->assertEquals($query, $query->not('group', 'admin'));
        $this->assertEquals($query, $query->not('group', null));
        $this->assertEquals($query, $query->not('name', ['Larry', 'Curly', 'Moe']));
        $this->assertEquals([['disabled', '<>', true], ['group', '<>', 'admin'], ['group', '<>', null], ['name', 'NOT IN', ['Larry', 'Curly', 'Moe']]], $query->getWhere()->getConditions());
    }

    public function testBetween()
    {
        $query = new UpdateQuery();

        $this->assertEquals($query, $query->between('date', 2015, 2016));
        $this->assertEquals([['BETWEEN', 'date', 2015, 2016, true]], $query->getWhere()->getConditions());
    }

    public function testNotBetween()
    {
        $query = new UpdateQuery();

        $this->assertEquals($query, $query->notBetween('date', 2015, 2016));
        $this->assertEquals([['BETWEEN', 'date', 2015, 2016, false]], $query->getWhere()->getConditions());
    }

    public function testExists()
    {
        $query = new UpdateQuery();

        $f = function (SelectQuery $query) {};

        $this->assertEquals($query, $query->exists($f));
        $this->assertEquals([['EXISTS', $f, true]], $query->getWhere()->getConditions());
    }

    public function testNotExists()
    {
        $query = new UpdateQuery();

        $f = function (SelectQuery $query) {};

        $this->assertEquals($query, $query->notExists($f));
        $this->assertEquals([['EXISTS', $f, false]], $query->getWhere()->getConditions());
    }

    public function testOrderBy()
    {
        $query = new UpdateQuery();

        $this->assertEquals($query, $query->orderBy('uid', 'ASC'));
        $orderBy = $query->getOrderBy();
        $this->assertInstanceOf('JAQB\Statement\OrderStatement', $orderBy);
        $this->assertFalse($orderBy->isGroupBy());
        $this->assertEquals([['uid', 'ASC']], $orderBy->getFields());
    }

    public function testLimit()
    {
        $query = new UpdateQuery();

        $this->assertEquals($query, $query->limit(10));
        $limit = $query->getLimit();
        $this->assertInstanceOf('JAQB\Statement\LimitStatement', $limit);
        $this->assertEquals(10, $limit->getLimit());

        $this->assertEquals($query, $query->limit('hello'));
        $this->assertEquals(10, $query->getLimit()->getLimit());
    }

    public function testBuild()
    {
        $query = new UpdateQuery();

        $query->table('Users')
              ->where('uid', 10)
              ->between('created_at', '2016-04-01', '2016-04-30')
              ->notBetween('balance', 100, 150)
              ->not('disabled')
              ->orWhere('admin', true)
              ->values(['test' => 'hello', 'test2' => 'field'])
              ->orderBy('uid', 'ASC')
              ->limit(100);

        // test for idempotence
        for ($i = 0; $i < 3; ++$i) {
            $this->assertEquals('UPDATE `Users` SET `test` = ?, `test2` = ? WHERE `uid` = ? AND `created_at` BETWEEN ? AND ? AND `balance` NOT BETWEEN ? AND ? AND `disabled` <> ? OR `admin` = ? ORDER BY `uid` ASC LIMIT 100', $query->build());

            // test values
            $this->assertEquals(['hello', 'field', 10, '2016-04-01', '2016-04-30', 100, 150, true, true], $query->getValues());
        }
    }

    public function testClone()
    {
        $query = new UpdateQuery();
        $query2 = clone $query;
        $this->assertNotSame($query->getTable(), $query2->getTable());
        $this->assertNotSame($query->getSet(), $query2->getSet());
        $this->assertNotSame($query->getWhere(), $query2->getWhere());
        $this->assertNotSame($query->getOrderBy(), $query2->getOrderBy());
        $this->assertNotSame($query->getLimit(), $query2->getLimit());
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
        $pdo->shouldReceive('prepare')->withArgs(['UPDATE `Test` WHERE `id` = ?'])
            ->andReturn($stmt);

        $query = new UpdateQuery();
        $query->setPDO($pdo);
        $this->assertEquals($pdo, $query->getPDO());
        $query->table('Test')->where('id', 'test');

        $this->assertEquals($stmt, $query->execute());
        $this->assertEquals(10, $query->rowCount());
    }

    public function testExecuteFail()
    {
        $stmt = Mockery::mock();
        $stmt->shouldReceive('execute')->andReturn(false);

        $pdo = Mockery::mock();
        $pdo->shouldReceive('prepare')->andReturn($stmt);

        $query = new UpdateQuery();
        $query->setPDO($pdo);

        $this->assertFalse($query->execute());
    }
}
