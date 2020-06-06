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

use JAQB\Statement\LimitStatement;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class LimitStatementTest extends MockeryTestCase
{
    public function testSetLimit()
    {
        $stmt = new LimitStatement();
        $this->assertEquals($stmt, $stmt->setLimit(100));
        $this->assertEquals(0, $stmt->getStart());
        $this->assertEquals(100, $stmt->getLimit());

        $this->assertEquals($stmt, $stmt->setLimit(50, 10));
        $this->assertEquals(10, $stmt->getStart());
        $this->assertEquals(50, $stmt->getLimit());
    }

    public function testSetLimitWithMax()
    {
        $stmt = new LimitStatement(50);
        $this->assertEquals($stmt, $stmt->setLimit(49));
        $this->assertEquals(49, $stmt->getLimit());

        $this->assertEquals($stmt, $stmt->setLimit(100));
        $this->assertEquals(50, $stmt->getLimit());
    }

    public function testBuild()
    {
        $stmt = new LimitStatement();

        $this->assertEquals($stmt, $stmt->setLimit(100));
        $this->assertEquals('LIMIT 100', $stmt->build());

        $this->assertEquals($stmt, $stmt->setLimit(50, 10));
        $this->assertEquals('LIMIT 10,50', $stmt->build());

        $stmt = new LimitStatement();
        $this->assertEquals('', $stmt->build());
    }
}
