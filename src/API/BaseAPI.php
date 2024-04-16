<?php

namespace Xatbot\Bot\API;

use Exception;

abstract class BaseAPI
{
    private static bool $init = false;

    private static int $botID = 0;
    private static int $bot = 0;
    private static $moduleName = null;


    /**
     * @throws Exception
     */
    final public static function init()
    {
        if (self::$init) {
            throw new Exception('API already initialized.');
        }

        self::$init = true;

        $return = [];
        $return['botID'] = &self::$botID;
        $return['bot'] = &self::$bot;
        $return['moduleName'] = &self::$moduleName;

        return $return;
    }

    /**
     * @throws Exception
     */
    final public static function getBotID()
    {
        if (!self::$init) {
            throw new \RuntimeException('API not initialized.');
        }

        return self::$botID;
    }

    /**
     * @throws Exception
     */
    final public static function getBot()
    {
        if (!self::$init) {
            throw new Exception('API not initalized.');
        }

        return self::$bot;
    }
}
