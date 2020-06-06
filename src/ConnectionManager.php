<?php

/**
 * @author Jared King <j@jaredtking.com>
 *
 * @see http://jaredtking.com
 *
 * @copyright 2015 Jared King
 * @license MIT
 */

namespace JAQB;

use InvalidArgumentException;
use JAQB\Exception\JAQBException;
use PDO;

/**
 * This class manages one or more PDO database connections.
 */
class ConnectionManager
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var array
     */
    private $connections = [];

    /**
     * @var string|false
     */
    private $default;

    /**
     * @var array
     */
    private static $connectionParams = [
        'host' => 'host',
        'port' => 'port',
        'name' => 'dbname',
        'charset' => 'charset',
    ];

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Gets a database connection by ID.
     *
     * @param string $id
     *
     * @throws JAQBException if the connection does not exist
     *
     * @return QueryBuilder
     */
    public function get($id)
    {
        if (isset($this->connections[$id])) {
            return $this->connections[$id];
        }

        if (!isset($this->config[$id])) {
            throw new JAQBException('No configuration or connection has been supplied for the ID "'.$id.'".');
        }

        $this->connections[$id] = $this->buildFromConfig($this->config[$id], $id);

        return $this->connections[$id];
    }

    /**
     * Gets the default database connection.
     *
     * @throws JAQBException if there is not a default connection
     *
     * @return QueryBuilder
     */
    public function getDefault()
    {
        // get the memoized default
        if ($this->default) {
            return $this->get($this->default);
        }

        // no configurations available
        // check for existing connections
        if (0 === count($this->config)) {
            if (1 === count($this->connections)) {
                $this->default = array_keys($this->connections)[0];

                return $this->get($this->default);
            } elseif (count($this->connections) > 1) {
                throw new JAQBException('Could not determine the default connection because multiple connections were available and the default has not been set.');
            }

            throw new JAQBException('The default connection is not available because no configurations have been supplied.');
        }

        // handle the case where there is a single configuration
        if (1 === count($this->config)) {
            $this->default = array_keys($this->config)[0];

            return $this->get($this->default);
        }

        // handle multiple configurations
        foreach ($this->config as $k => $v) {
            if (isset($v['default'])) {
                $this->default = $k;

                return $this->get($this->default);
            }
        }

        throw new JAQBException('There is no default connection.');
    }

    /**
     * Adds a connection.
     *
     * @param string       $id
     * @param QueryBuilder $connection
     *
     * @throws InvalidArgumentException if a connection with the given ID already exists
     *
     * @return $this
     */
    public function add($id, QueryBuilder $connection)
    {
        if (isset($this->connections[$id])) {
            throw new InvalidArgumentException('A connection with the ID "'.$id.'" already exists.');
        }

        $this->connections[$id] = $connection;
        $this->default = false;

        return $this;
    }

    /**
     * Builds a new query builder instance from a configuration.
     * NOTE: This is not intended to be used outside of this class.
     *
     * @param array  $config
     * @param string $id
     *
     * @throws JAQBException
     *
     * @return QueryBuilder
     */
    public function buildFromConfig(array $config, $id)
    {
        // generate the dsn needed for PDO
        if (isset($config['dsn'])) {
            $dsn = $config['dsn'];
        } else {
            $dsn = $this->buildDsn($config, $id);
        }

        $user = isset($config['user']) ? $config['user'] : null;
        $password = isset($config['password']) ? $config['password'] : null;
        $options = isset($config['options']) ? $config['options'] : [];

        $pdo = new PDO($dsn, $user, $password, $options);

        return new QueryBuilder($pdo);
    }

    /**
     * Builds a PDO DSN string from a JAQB connection configuration.
     *
     * @param array  $config
     * @param string $id     configuration ID
     *
     * @throws JAQBException if the configuration is invalid
     *
     * @return string
     */
    public function buildDsn(array $config, $id)
    {
        if (!isset($config['type'])) {
            throw new JAQBException('Missing connection type for configuration "'.$id.'"!');
        }

        $dsn = $config['type'].':';
        $params = [];
        foreach (self::$connectionParams as $j => $k) {
            if (isset($config[$j])) {
                $params[] = $k.'='.$config[$j];
            }
        }
        $dsn .= implode(';', $params);

        return $dsn;
    }
}
