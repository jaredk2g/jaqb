<?php

/**
 * @author Jared King <j@jaredtking.com>
 *
 * @link http://jaredtking.com
 *
 * @copyright 2015 Jared King
 * @license MIT
 */
use JAQB\Statement\WhereStatement;

class WhereStatementTest extends \PHPUnit_Framework_TestCase
{
    public function testHaving()
    {
        $stmt = new WhereStatement();
        $this->assertFalse($stmt->isHaving());

        $stmt = new WhereStatement(true);
        $this->assertTrue($stmt->isHaving());
    }

    public function testAddConditionString()
    {
        $stmt = new WhereStatement();
        $this->assertEquals($stmt, $stmt->addCondition('user_id', 10));
        $this->assertEquals([['user_id', '=', 10]], $stmt->getConditions());

        $this->assertEquals('WHERE `user_id`=?', $stmt->build());
        $this->assertEquals([10], $stmt->getValues());
    }

    public function testAddConditionWithOperator()
    {
        $stmt = new WhereStatement();
        $this->assertEquals($stmt, $stmt->addCondition('user_id', 10));
        $this->assertEquals($stmt, $stmt->addCondition('created_at', 100, '>'));
        $this->assertEquals([['user_id', '=', 10], ['created_at', '>', 100]], $stmt->getConditions());

        $this->assertEquals('WHERE `user_id`=? AND `created_at`>?', $stmt->build());
        $this->assertEquals([10, 100], $stmt->getValues());
    }

    public function testAddConditionSql()
    {
        $stmt = new WhereStatement();
        $this->assertEquals($stmt, $stmt->addCondition('user_id > 10'));
        $this->assertEquals([['user_id > 10']], $stmt->getConditions());

        $this->assertEquals('WHERE user_id > 10', $stmt->build());
        $this->assertEquals([], $stmt->getValues());
    }

    public function testAddConditionKeyValue()
    {
        $stmt = new WhereStatement();
        $this->assertEquals($stmt, $stmt->addCondition(['field1' => 'value', 'field2' => false]));
        $this->assertEquals([['field1', '=', 'value'], ['field2', '=', false]], $stmt->getConditions());

        $this->assertEquals('WHERE `field1`=? AND `field2`=?', $stmt->build());
        $this->assertEquals(['value', false], $stmt->getValues());
    }

    public function testAddConditionArray()
    {
        $stmt = new WhereStatement();
        $this->assertEquals($stmt, $stmt->addCondition([['field', 'value'], ['field2', 'value2', 'like']]));
        $this->assertEquals([['field', '=', 'value'], ['field2', 'like', 'value2']], $stmt->getConditions());

        $this->assertEquals('WHERE `field`=? AND `field2`like?', $stmt->build());
        $this->assertEquals(['value', 'value2'], $stmt->getValues());

        $stmt = new WhereStatement();
        $this->assertEquals($stmt, $stmt->addCondition(['first_name LIKE "%john%"', 'last_name LIKE "%doe%"']));
        $this->assertEquals([['first_name LIKE "%john%"'], ['last_name LIKE "%doe%"']], $stmt->getConditions());

        $this->assertEquals('WHERE first_name LIKE "%john%" AND last_name LIKE "%doe%"', $stmt->build());
        $this->assertEquals([], $stmt->getValues());
    }

    public function testAddConditionMixed()
    {
        $stmt = new WhereStatement();
        $this->assertEquals($stmt, $stmt->addCondition(['field' => 'value', ['field2', 'value2', 'like']]));
        $this->assertEquals([['field', '=', 'value'], ['field2', 'like', 'value2']], $stmt->getConditions());

        $this->assertEquals('WHERE `field`=? AND `field2`like?', $stmt->build());
        $this->assertEquals(['value', 'value2'], $stmt->getValues());
    }

    public function testAddConditionNull()
    {
        $stmt = new WhereStatement();
        $this->assertEquals($stmt, $stmt->addCondition(['field' => null, ['field2', null]]));

        $this->assertEquals('WHERE `field` IS NULL AND `field2` IS NULL', $stmt->build());
        $this->assertEquals([], $stmt->getValues());
    }

    public function testAddNestedCondition()
    {
        $this->markTestIncomplete();
    }

    public function testBuild()
    {
        $stmt = new WhereStatement();
        $this->assertEquals('', $stmt->build());

        $stmt->addCondition('field1', 'value')->addCondition('field2', 'value2')
             ->addCondition(['should"_not===_work' => 'fail']);
        $this->assertEquals('WHERE `field1`=? AND `field2`=?', $stmt->build());

        $stmt = new WhereStatement(true);
        $stmt->addCondition('field1', 'value')->addCondition('field2', 'value2');
        $this->assertEquals('HAVING `field1`=? AND `field2`=?', $stmt->build());
    }
}
