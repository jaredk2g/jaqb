<?php

/**
 * @author Jared King <j@jaredtking.com>
 *
 * @see http://jaredtking.com
 *
 * @copyright 2015 Jared King
 * @license MIT
 */
use JAQB\ConnectionManager;
use JAQB\Exception\JAQBException;
use JAQB\QueryBuilder;

class ConnectionManagerTest extends PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $config = [
            'test' => [
                'dsn' => 'sqlite:'.__DIR__.'/test.sqlite',
            ],
            'db2' => [
                'dsn' => 'sqlite:'.__DIR__.'/test2.sqlite',
            ],
        ];
        $manager = new ConnectionManager($config);

        $conn1 = $manager->get('test');
        $conn2 = $manager->get('db2');
        $this->assertInstanceOf(QueryBuilder::class, $conn1);
        $this->assertInstanceOf(QueryBuilder::class, $conn2);

        $this->assertInstanceOf(PDO::class, $conn1->getPDO());
        $this->assertInstanceOf(PDO::class, $conn2->getPDO());

        $this->assertEquals($conn1, $manager->get('test'));
        $this->assertEquals($conn2, $manager->get('db2'));
    }

    public function testGetDoesNotExist()
    {
        $this->expectException(JAQBException::class);

        $manager = new ConnectionManager();
        $manager->get('does not exist');
    }

    public function testGetDefault()
    {
        $this->expectException(JAQBException::class);

        $manager = new ConnectionManager();
        $manager->getDefault();
    }

    public function testGetDefaultDoesNotExist()
    {
        $manager = new ConnectionManager();
        $connection = new QueryBuilder();
        $manager->add('test', $connection);

        $connection2 = $manager->getDefault();
        $this->assertEquals($connection, $connection2);
    }

    public function testAdd()
    {
        $manager = new ConnectionManager();
        $connection = new QueryBuilder();
        $this->assertEquals($manager, $manager->add('test_add', $connection));
        $this->assertEquals($connection, $manager->get('test_add'));
    }

    public function testAddExisting()
    {
        $this->expectException(InvalidArgumentException::class);

        $manager = new ConnectionManager();
        $connection = new QueryBuilder();
        $manager->add('test', $connection);
        $manager->add('test', $connection);
    }

    public function testBuildFromConfig()
    {
        // should fail because there is no mysql server running
        $this->expectException(PDOException::class);

        $config = [
            'type' => 'mysql',
            'host' => 'localhost',
            'name' => 'test',
            'username' => 'root',
            'password' => 'password',
            'errorMode' => PDO::ERRMODE_SILENT,
        ];

        $manager = new ConnectionManager();
        $connection = $manager->buildFromConfig($config, 'test');

        $this->assertInstanceOf(QueryBuilder::class, $connection);
        $this->assertInstanceOf(PDO::class, $connection->getPDO());
    }

    public function testBuildFromConfigDsn()
    {
        $config = [
            'dsn' => 'sqlite:'.__DIR__.'/test.sqlite',
        ];

        $manager = new ConnectionManager();
        $connection = $manager->buildFromConfig($config, 'test');

        $this->assertInstanceOf(QueryBuilder::class, $connection);
        $this->assertInstanceOf(PDO::class, $connection->getPDO());
    }

    public function testBuildFromConfigNoCredentials()
    {
        // should fail because there is no mysql server running
        $this->expectException(PDOException::class);

        $config = [
            'type' => 'mysql',
            'host' => 'localhost',
            'name' => 'test',
            'errorMode' => PDO::ERRMODE_SILENT,
        ];

        $manager = new ConnectionManager();
        $manager->buildFromConfig($config, 'test');
    }

    public function testBuildFromConfigErrorMode()
    {
        $config = [
            'dsn' => 'sqlite:'.__DIR__.'/test.sqlite',
            'errorMode' => PDO::ERRMODE_SILENT,
        ];

        $manager = new ConnectionManager();
        $connection = $manager->buildFromConfig($config, 'test');

        $this->assertInstanceOf(QueryBuilder::class, $connection);
        $this->assertInstanceOf(PDO::class, $connection->getPDO());
    }

    public function testBuildDsn()
    {
        $config = [
            'type' => 'mysql',
            'host' => 'localhost',
            'port' => '3306',
            'name' => 'test',
            'charset' => 'utf8',
            'username' => 'test',
            'password' => 'password',
            'errorMode' => PDO::ERRMODE_EXCEPTION,
        ];

        $manager = new ConnectionManager();
        $dsn = $manager->buildDsn($config, 'test');

        $this->assertEquals('mysql:host=localhost;port=3306;dbname=test;charset=utf8', $dsn);
    }

    public function testBuildDsnMissingType()
    {
        $this->expectException(JAQBException::class);

        $manager = new ConnectionManager();
        $manager->buildDsn([], 'test');
    }
}
