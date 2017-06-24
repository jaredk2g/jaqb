<?php

/**
 * @author Jared King <j@jaredtking.com>
 *
 * @see http://jaredtking.com
 *
 * @copyright 2015 Jared King
 * @license MIT
 */
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
        $qb = new QueryBuilder();

        $query = $qb->select();
        $this->assertInstanceOf('JAQB\Query\SelectQuery', $query);
        $this->assertEquals(['*'], $query->getSelect()->getFields());

        $query = $qb->select('test');
        $this->assertInstanceOf('JAQB\Query\SelectQuery', $query);
        $this->assertEquals(['test'], $query->getSelect()->getFields());
    }

    public function testInsert()
    {
        $qb = new QueryBuilder();

        $query = $qb->insert(['test' => 'hello']);
        $this->assertInstanceOf('JAQB\Query\InsertQuery', $query);
        $this->assertEquals(['test' => 'hello'], $query->getInsertValues()->getInsertValues());
    }

    public function testUpdate()
    {
        $qb = new QueryBuilder();

        $query = $qb->update('Users');
        $this->assertInstanceOf('JAQB\Query\UpdateQuery', $query);
        $this->assertEquals(['Users'], $query->getTable()->getTables());
    }

    public function testDelete()
    {
        $qb = new QueryBuilder();

        $query = $qb->delete('Users');
        $this->assertInstanceOf('JAQB\Query\DeleteQuery', $query);
        $this->assertEquals(['Users'], $query->getFrom()->getTables());
    }

    public function testRaw()
    {
        $qb = new QueryBuilder();

        $query = $qb->raw('TRUNCATE TABLE Users');
        $this->assertInstanceOf('JAQB\Query\SqlQuery', $query);
        $this->assertEquals('TRUNCATE TABLE Users', $query->build());
    }
}
