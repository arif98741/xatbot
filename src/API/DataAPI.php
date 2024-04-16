<?php

namespace Xatbot\Bot\API;

class DataAPI extends BaseAPI
{
    private static $data;

    public static function dumpVars()
    {
        var_dump(self::$data);
    }

    //::////////////////////////////////////////////////////////////////////////
    //::// Overloading methods
    //::////////////////////////////////////////////////////////////////////////

    /**
     * @throws \Exception
     */
    public static function set($name, $value)
    {
        self::$data[self::getBotID()][$name] = $value;
    }

    public static function get($name)
    {
        return self::$data[self::getBotID()][$name];
    }

    /**
     * @throws \Exception
     */
    public static function isSetVariable($name)
    {
        return (isset(self::$data[self::getBotID()][$name]));
    }

    /**
     * @throws \Exception
     */
    public static function unSetVariable($name)
    {
        unset(self::$data[self::getBotID()][$name]);
    }
}
