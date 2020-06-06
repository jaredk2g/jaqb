<?php

/**
 * @author Jared King <j@jaredtking.com>
 *
 * @link http://jaredtking.com
 *
 * @copyright 2015 Jared King
 * @license MIT
 */

namespace JAQB\Tests;

use Mockery;
use Pimple\Container;
use JAQB\Session;

class SessionTest extends Mockery\Adapter\Phpunit\MockeryTestCase
{
    public static $mock;

    public function tearDown()
    {
        self::$mock = false;
    }

    public function testInstall()
    {
        $c = new Container();
        $stmt = Mockery::mock('PDOStatement');
        $execute = Mockery::mock();
        $execute->shouldReceive('execute')->andReturn($stmt);
        $db = Mockery::mock();
        $db->shouldReceive('raw')->withArgs(['CREATE TABLE IF NOT EXISTS `Sessions` (`id` varchar(32) NOT NULL, PRIMARY KEY (`id`), `session_data` longtext NULL, `access` int(10) NULL);'])->andReturn($execute)->once();
        $c['db'] = $db;

        $session = new Session($c);

        $this->assertTrue($session->install());
    }

    public function testRegisterHandler()
    {
        $c = new Container();
        $session = new Session($c);

        self::$mock = \Mockery::mock('php');
        self::$mock->shouldReceive('session_set_save_handler')->withArgs([$session, true])->andReturn(true)->once();

        $this->assertTrue(Session::registerHandler($session));
    }

    public function testRead()
    {
        $c = new Container();
        $scalar = Mockery::mock();
        $scalar->shouldReceive('scalar')->andReturn('data');
        $where = Mockery::mock();
        $where->shouldReceive('where')->withArgs(['id', '_id_'])->andReturn($scalar);
        $from = Mockery::mock();
        $from->shouldReceive('from')->withArgs(['Sessions'])->andReturn($where);
        $c['db'] = Mockery::mock();
        $c['db']->shouldReceive('select')->withArgs(['session_data'])->andReturn($from);

        $session = new Session($c);
        $this->assertEquals('data', $session->read('_id_'));
    }

    public function testWrite()
    {
        $c = new Container();

        $stmt = Mockery::mock('PDOStatement');

        // mock delete query
        $execute = Mockery::mock();
        $execute->shouldReceive('execute')->andReturn($stmt);
        $where = Mockery::mock();
        $where->shouldReceive('where')->withArgs(['id', '_id_'])->andReturn($execute);
        $db = Mockery::mock();
        $db->shouldReceive('delete')->withArgs(['Sessions'])->andReturn($where);

        // mock insert query
        $execute = Mockery::mock();
        $execute->shouldReceive('execute')->andReturn($stmt);
        $where = Mockery::mock();
        $where->shouldReceive('into')->withArgs(['Sessions'])->andReturn($execute);
        $db->shouldReceive('insert')->withArgs([['id' => '_id_', 'access' => time(), 'session_data' => 'data']])->andReturn($where);

        $c['db'] = $db;

        $session = new Session($c);
        $this->assertTrue($session->write('_id_', 'data'));
    }

    public function testDestroy()
    {
        $c = new Container();
        $stmt = Mockery::mock('PDOStatement');
        $execute = Mockery::mock();
        $execute->shouldReceive('execute')->andReturn($stmt);
        $where = Mockery::mock();
        $where->shouldReceive('where')->withArgs(['id', '_id_'])->andReturn($execute);
        $c['db'] = Mockery::mock();
        $c['db']->shouldReceive('delete')->withArgs(['Sessions'])->andReturn($where);

        $session = new Session($c);
        $this->assertTrue($session->destroy('_id_'));
    }

    public function testGC()
    {
        $c = new Container();
        $stmt = Mockery::mock('PDOStatement');
        $execute = Mockery::mock();
        $execute->shouldReceive('execute')->andReturn($stmt);
        $where = Mockery::mock();
        $where->shouldReceive('where')->withArgs(['access', time() - 100, '<'])->andReturn($execute);
        $c['db'] = Mockery::mock();
        $c['db']->shouldReceive('delete')->withArgs(['Sessions'])->andReturn($where);

        $session = new Session($c);
        $this->assertTrue($session->gc(100));
    }

    public function testOpen()
    {
        $c = new Container();
        $session = new Session($c);
        $this->assertTrue($session->open('path', 'name'));
    }

    public function testClose()
    {
        $c = new Container();
        $session = new Session($c);
        $this->assertTrue($session->close());
    }
}

include_once __DIR__.'/session_save_handler.php';