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
     * @param $name
     * @param $value
     * @return void
     * @throws \Exception
     */
    public static function set($name, $value)
    {
        self::$data[self::getBotID()][$name] = $value;
    }

    /**
     * @param string $name
     * @return mixed
     * @throws \Exception
     */
    public static function get(string $name)
    {
        return self::$data[self::getBotID()][$name];
    }

    /**
     * @param string $name
     * @return bool
     * @throws \Exception
     */
    public static function issetVariable(string $name)
    {
        return (isset(self::$data[self::getBotID()][$name]));
    }

    /**
     * @param string $name
     * @return void
     * @throws \Exception
     */
    public static function unsetVariable(string $name)
    {
        unset(self::$data[self::getBotID()][$name]);
    }
}
