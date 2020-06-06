<?php

/**
 * @author Jared King <j@jaredtking.com>
 *
 * @see http://jaredtking.com
 *
 * @copyright 2015 Jared King
 * @license MIT
 */

namespace JAQB\Tests\Services;

use Infuse\Application;
use JAQB\QueryBuilder;
use JAQB\Services\Database;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery;
use PDO;

class DatabaseServiceTest extends MockeryTestCase
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
        $this->assertInstanceOf(QueryBuilder::class, $db);
        $this->assertEquals($app['pdo'], $db->getPdo());
    }
}
