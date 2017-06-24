<?php

/**
 * @author Jared King <j@jaredtking.com>
 *
 * @see http://jaredtking.com
 *
 * @copyright 2015 Jared King
 * @license MIT
 */
use Infuse\Application;
use JAQB\Services\Database;

class DatabaseTest extends PHPUnit_Framework_TestCase
{
    public function testInvoke()
    {
        $config = [
            'database' => [
                'type' => 'mysql',
                'host' => 'localhost',
                'name' => 'mydb',
                'user' => 'root',
                'password' => '',
            ],
        ];
        $app = new Application($config);
        $app['pdo'] = Mockery::mock(PDO::class);
        $service = new Database();

        $db = $service($app);
        $this->assertInstanceOf('JAQB\QueryBuilder', $db);
        $this->assertEquals($app['pdo'], $db->getPdo());
    }
}
