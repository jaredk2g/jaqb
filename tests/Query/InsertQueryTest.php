<?php

/**
 * @package JAQB
 * @author Jared King <j@jaredtking.com>
 * @link http://jaredtking.com
 * @copyright 2015 Jared King
 * @license MIT
 */

use JAQB\Query\InsertQuery;

class InsertQueryTest extends \PHPUnit_Framework_TestCase
{
    public function testTable()
    {
        $query = new InsertQuery();

        $this->assertEquals($query, $query->into('Users'));
        $this->assertInstanceOf('\\JAQB\\Statement\\FromStatement', $query->getInto());
        $this->assertFalse($query->getInto()->hasFrom());
        $this->assertEquals(['Users'], $query->getInto()->getTables());
    }

    public function testValues()
    {
        $query = new InsertQuery();

        $this->assertEquals($query, $query->values(['test1' => 1, 'test2' => 2]));
        $this->assertEquals($query, $query->values(['test3' => 3]));
        $this->assertInstanceOf('\\JAQB\\Statement\\ValuesStatement', $query->getInsertValues());
        $this->assertEquals(['test1' => 1, 'test2' => 2, 'test3' => 3], $query->getInsertValues()->getValues());
    }

    public function testBuild()
    {
        $query = new InsertQuery();

        $query->into('Users')->values(['field1' => 'what', 'field2' => 'test']);

        $this->assertEquals('INSERT INTO `Users` (`field1`,`field2`) VALUES (?,?)', $query->build());

        // test values
        $this->assertEquals(['what', 'test'], $query->getValues());
    }
}
