<?php

namespace JAQB;

use JAQB\Tests\SessionTest;

function session_set_save_handler($arg1, $arg2 = true)
{
    return SessionTest::$mock ? SessionTest::$mock->session_set_save_handler($arg1, $arg2) : \session_set_save_handler($arg1, $arg2);
}