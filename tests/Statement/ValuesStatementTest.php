<?php

/**
 * @author Jared King <j@jaredtking.com>
 *
 * @see http://jaredtking.com
 *
 * @copyright 2015 Jared King
 * @license MIT
 */
use JAQB\Statement\ValuesStatement;

class ValuesStatementTest extends PHPUnit_Framework_TestCase
{
    public function testValues()
    {
        $stmt = new ValuesStatement();
        $this->assertEquals($stmt, $stmt->addValues(['test' => 1, 'test2' => 2]));
        $this->assertEquals($stmt, $stmt->addValues(['test3' => 3]));
        $this->assertEquals(['test' => 1, 'test2' => 2, 'test3' => 3], $stmt->getInsertValues());
        $this->assertEquals([['test' => 1, 'test2' => 2, 'test3' => 3]], $stmt->getInsertRows());
    }

    public function testBuild()
    {
        $stmt = new ValuesStatement();
        $this->assertEquals('', $stmt->build());

        $stmt->addValues(['test' => 1, 'should"_not===_work' => 'fail']);
        $this->assertEquals('(`test`) VALUES (?)', $stmt->build());
        $this->assertEquals([1], $stmt->getValues());

        $stmt->addValues(['test2' => 2]);
        $this->assertEquals('(`test`, `test2`) VALUES (?, ?)', $stmt->build());
        $this->assertEquals([1, 2], $stmt->getValues());
    }

    public function testBuildBatch()
    {
        $stmt = new ValuesStatement();
        $this->assertEquals('', $stmt->build());

        $stmt->addValues([['test' => 1, 'should"_not===_work' => 'fail'], ['test' => 2, 'should"_not===_work' => 'fail']]);
        $this->assertEquals('(`test`) VALUES (?), (?)', $stmt->build());
        $this->assertEquals([1, 2], $stmt->getValues());

        $stmt->addValues([['test' => 3, 'test2' => 3], ['test' => 4, 'test2' => 4]]);
        $this->assertEquals('(`test`) VALUES (?), (?), (?), (?)', $stmt->build());
        $this->assertEquals([1, 2, 3, 4], $stmt->getValues());
    }
}
