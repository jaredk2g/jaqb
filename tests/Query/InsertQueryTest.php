<?php

/**
 * @author Jared King <j@jaredtking.com>
 *
 * @see http://jaredtking.com
 *
 * @copyright 2015 Jared King
 * @license MIT
 */
use JAQB\Query\InsertQuery;
use JAQB\Statement\FromStatement;
use JAQB\Statement\ValuesStatement;

class InsertQueryTest extends PHPUnit_Framework_TestCase
{
    public function testTable()
    {
        $query = new InsertQuery();

        $this->assertEquals($query, $query->into('Users'));
        $this->assertInstanceOf(FromStatement::class, $query->getInto());
        $this->assertEquals(['Users'], $query->getInto()->getTables());
    }

    public function testValues()
    {
        $query = new InsertQuery();

        $this->assertEquals($query, $query->values(['test1' => 1, 'test2' => 2]));
        $this->assertEquals($query, $query->values(['test3' => 3]));
        $this->assertInstanceOf(ValuesStatement::class, $query->getInsertValues());
        $this->assertEquals(['test1' => 1, 'test2' => 2, 'test3' => 3], $query->getInsertValues()->getInsertValues());
    }

    public function testBuild()
    {
        $query = new InsertQuery();

        $query->into('Users')
              ->values(['field1' => 'what', 'field2' => 'test']);

        // test for idempotence
        for ($i = 0; $i < 3; ++$i) {
            $this->assertEquals('INSERT INTO `Users` (`field1`, `field2`) VALUES (?, ?)', $query->build());

            // test values
            $this->assertEquals(['what', 'test'], $query->getValues());
        }
    }

    public function testBuildBatch()
    {
        $query = new InsertQuery();

        $query->into('Users')
            ->values([
                ['field1' => 'what', 'field2' => 'test'],
                ['field1' => 'what2', 'field2' => 'test2'],
                ['field1' => 'what3', 'field2' => 'test3'], ]);

        // test for idempotence
        for ($i = 0; $i < 3; ++$i) {
            $this->assertEquals('INSERT INTO `Users` (`field1`, `field2`) VALUES (?, ?), (?, ?), (?, ?)', $query->build());

            // test values
            $this->assertEquals(['what', 'test', 'what2', 'test2', 'what3', 'test3'], $query->getValues());
        }
    }

    public function testClone()
    {
        $query = new InsertQuery();
        $query2 = clone $query;
        $query2->into('Blah');
        $query2->values(['test']);
        $this->assertNotSame($query->getInto(), $query2->getInto());
        $this->assertNotSame($query->getInsertValues(), $query2->getInsertValues());
    }

    ////////////////////////
    // Operations
    ////////////////////////

    public function testExecute()
    {
        $stmt = Mockery::mock();
        $stmt->shouldReceive('execute')->andReturn(true);
        $stmt->shouldReceive('rowCount')->andReturn(10);

        $pdo = Mockery::mock(PDO::class);
        $pdo->shouldReceive('prepare')->withArgs(['INSERT INTO `Test`'])
            ->andReturn($stmt);

        $query = new InsertQuery();
        $query->setPDO($pdo);
        $this->assertEquals($pdo, $query->getPDO());
        $query->into('Test');

        $this->assertEquals($stmt, $query->execute());
        $this->assertEquals(10, $query->rowCount());
    }

    public function testExecuteFail()
    {
        $stmt = Mockery::mock();
        $stmt->shouldReceive('execute')->andReturn(false);

        $pdo = Mockery::mock(PDO::class);
        $pdo->shouldReceive('prepare')->andReturn($stmt);

        $query = new InsertQuery();
        $query->setPDO($pdo);

        $this->assertFalse($query->execute());
    }
}
