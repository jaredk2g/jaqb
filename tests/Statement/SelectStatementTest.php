<?php

/**
 * @author Jared King <j@jaredtking.com>
 *
 * @link http://jaredtking.com
 *
 * @copyright 2015 Jared King
 * @license MIT
 */

namespace JAQB\Tests\Statement;

use JAQB\Statement\SelectStatement;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class SelectStatementTest extends MockeryTestCase
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
        $this->assertEquals(['*', 'test', 'test2'], $stmt->getFields());

        $this->assertEquals($stmt, $stmt->addFields(['test3']));
        $this->assertEquals(['*', 'test', 'test2', 'test3'], $stmt->getFields());
    }

    public function testAddFieldsString()
    {
        $stmt = new SelectStatement();
        $stmt->clearFields();
        $this->assertEquals($stmt, $stmt->addFields('test'));
        $this->assertEquals(['test'], $stmt->getFields());

        $stmt = new SelectStatement();
        $stmt->clearFields();
        $this->assertEquals($stmt, $stmt->addFields('test, test2'));
        $this->assertEquals(['test', 'test2'], $stmt->getFields());
    }

    public function testBuild()
    {
        $stmt = new SelectStatement();
        $stmt->clearFields();
        $this->assertEquals($stmt, $stmt->addFields('test,test2 AS blah,sum(p.amount)/count(p.*),should"_not===_work'));
        $this->assertEquals('SELECT `test`, `test2` AS `blah`, sum(p.amount)/count(p.*)', $stmt->build());

        $stmt = new SelectStatement();
        $this->assertEquals('SELECT *', $stmt->build());
    }
}
