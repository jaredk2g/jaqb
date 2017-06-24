<?php

/**
 * @author Jared King <j@jaredtking.com>
 *
 * @see http://jaredtking.com
 *
 * @copyright 2015 Jared King
 * @license MIT
 */

namespace JAQB\Services;

class ConnectionManager
{
    public function __invoke($app)
    {
        return new \JAQB\ConnectionManager($app['config']->get('database', []));
    }
}
