<?php

/**
 * @package infuse\libs
 * @author Jared King <j@jaredtking.com>
 * @link http://jaredtking.com
 * @copyright 2015 Jared King
 * @license MIT
 */

namespace JAQB;

use Pimple\Container;

class Session implements \SessionHandlerInterface
{
    const TABLENAME = 'Sessions';

    /**
     * @var Container
     */
    private $app;

    /**
     * Starts the session using this handler
     *
     * @param Session $app
     *
     * @return boolean
     */
    public static function registerHandler(Session $handler)
    {
        return session_set_save_handler($handler, true);
    }

    /**
     * Creates a new session object
     *
     * @param Container $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * Installs schema for handling sessions in a database
     *
     * @return boolean success
     */
    public function install()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `'.self::TABLENAME.'` (`id` varchar(32) NOT NULL, PRIMARY KEY (`id`), `session_data` longtext NULL, `access` int(10) NULL);';

        return $this->app['db']->raw($sql)->execute();
    }

    /**
     * Reads a session
     *
     * @param int $id session ID
     *
     * @return string data
     */
    public function read($id)
    {
        return $this->app['db']->select('session_data')
            ->from(self::TABLENAME)->where('id', $id)->scalar();
    }

    /**
     * Writes a session
     *
     * @param int    $id   session ID
     * @param string $data session data
     *
     * @return boolean success
     */
    public function write($id, $data)
    {
        $this->app['db']->delete(self::TABLENAME)
            ->where('id', $id)->execute();

        return $this->app['db']->insert([
                'id' => $id,
                'access' => time(),
                'session_data' => $data, ])
            ->into(self::TABLENAME)->execute();
    }

    /**
     * Destroys a session
     *
     * @param int $id session ID
     *
     * @return boolean success
     */
    public function destroy($id)
    {
        return $this->app['db']->delete(self::TABLENAME)
            ->where('id', $id)->execute();
    }

    /**
     * Performs garbage collection on sessions.
     *
     * @param int $max maximum number of seconds a session can live
     *
     * @return boolean success
     */
    public function gc($max)
    {
        // delete sessions older than max TTL
        $ttl = time() - $max;

        return $this->app['db']->delete(self::TABLENAME)
            ->where('access', $ttl, '<')->execute();
    }

    /**
     * These functions are all noops for various reasons...
     * open() and close() have no practical meaning in terms of database connections
     */
    public function open($path, $name)
    {
        return true;
    }

    public function close()
    {
        return true;
    }
}
