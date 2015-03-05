<?php

/**
 * @package JAQB
 * @author Jared King <j@jaredtking.com>
 * @link http://jaredtking.com
 * @copyright 2015 Jared King
 * @license MIT
 */

use JAQB\Statement\SelectStatement;

class SelectStatementTest extends \PHPUnit_Framework_TestCase
{
    public function testNoFields()
    {
        $stmt = new SelectStatement();
        $this->assertEquals(['*'], $stmt->getFields());
    }

    public function testAddFields()
    {
        $stmt = new SelectStatement();
        $this->assertEquals($stmt, $stmt->addFields(['test', 'test2']));
        $this->assertEquals(['test', 'test2'], $stmt->getFields());

        $this->assertEquals($stmt, $stmt->addFields(['test3']));
        $this->assertEquals(['test', 'test2', 'test3'], $stmt->getFields());
    }

    public function testAddFieldsString()
    {
        $stmt = new SelectStatement();
        $this->assertEquals($stmt, $stmt->addFields('test'));
        $this->assertEquals(['test'], $stmt->getFields());

        $stmt = new SelectStatement();
        $this->assertEquals($stmt, $stmt->addFields('test, test2'));
        $this->assertEquals(['test', 'test2'], $stmt->getFields());
    }

    public function testBuild()
    {
        $stmt = new SelectStatement();
        $this->assertEquals($stmt, $stmt->addFields('test,test2 AS blah,sum(p.amount)/count(p.*),should"_not===_work'));
        $this->assertEquals('SELECT `test`,`test2` AS `blah`,sum(p.amount)/count(p.*)', $stmt->build());

        $stmt = new SelectStatement();
        $this->assertEquals('SELECT *', $stmt->build());
    }
}
