<?php

/**
 * @author Jared King <j@jaredtking.com>
 *
 * @see http://jaredtking.com
 *
 * @copyright 2015 Jared King
 * @license MIT
 */
use JAQB\Query\DeleteQuery;
use JAQB\Query\InsertQuery;
use JAQB\Query\SelectQuery;
use JAQB\Query\SqlQuery;
use JAQB\Query\UpdateQuery;
use JAQB\QueryBuilder;

class QueryBuilderTest extends PHPUnit_Framework_TestCase
{
    public function testPDO()
    {
        $pdo = Mockery::mock(PDO::class);
        $qb = new QueryBuilder($pdo);
        $this->assertEquals($pdo, $qb->getPDO());
    }

    public function testSelect()
    {
        $pdo = Mockery::mock(PDO::class);
        $qb = new QueryBuilder($pdo);

        $query = $qb->select();
        $this->assertInstanceOf(SelectQuery::class, $query);
        $this->assertEquals(['*'], $query->getSelect()->getFields());

        $query = $qb->select('test');
        $this->assertInstanceOf(SelectQuery::class, $query);
        $this->assertEquals(['test'], $query->getSelect()->getFields());
    }

    public function testInsert()
    {
        $pdo = Mockery::mock(PDO::class);
        $qb = new QueryBuilder($pdo);

        $query = $qb->insert(['test' => 'hello']);
        $this->assertInstanceOf(InsertQuery::class, $query);
        $this->assertEquals(['test' => 'hello'], $query->getInsertValues()->getInsertValues());
    }

    public function testUpdate()
    {
        $pdo = Mockery::mock(PDO::class);
        $qb = new QueryBuilder($pdo);

        $query = $qb->update('Users');
        $this->assertInstanceOf(UpdateQuery::class, $query);
        $this->assertEquals(['Users'], $query->getTable()->getTables());
    }

    public function testDelete()
    {
        $pdo = Mockery::mock(PDO::class);
        $qb = new QueryBuilder($pdo);

        $query = $qb->delete('Users');
        $this->assertInstanceOf(DeleteQuery::class, $query);
        $this->assertEquals(['Users'], $query->getFrom()->getTables());
    }

    public function testRaw()
    {
        $pdo = Mockery::mock(PDO::class);
        $qb = new QueryBuilder($pdo);

        $query = $qb->raw('TRUNCATE TABLE Users');
        $this->assertInstanceOf(SqlQuery::class, $query);
        $this->assertEquals('TRUNCATE TABLE Users', $query->build());
    }

    public function testTransactions()
    {
        $pdo = Mockery::mock(PDO::class);
        $pdo->shouldReceive('beginTransaction')->andReturn(true);
        $pdo->shouldReceive('rollBack')->andReturn(true);
        $pdo->shouldReceive('commit')->andReturn(true);
        $pdo->shouldReceive('inTransaction')->andReturn(true);

        $qb = new QueryBuilder($pdo);

        $this->assertTrue($qb->beginTransaction());
        $this->assertTrue($qb->inTransaction());
        $this->assertTrue($qb->commit());
        $this->assertTrue($qb->rollBack());
    }

    public function testLastInsertId()
    {
        $pdo = Mockery::mock(PDO::class);
        $pdo->shouldReceive('lastInsertId')->andReturn(1);

        $qb = new QueryBuilder($pdo);

        $this->assertEquals(1, $qb->lastInsertId());
    }
}
