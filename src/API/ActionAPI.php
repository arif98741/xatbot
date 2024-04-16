<?php

namespace Xatbot\Bot\API;

class ActionAPI extends BaseAPI
{
    /**
     * @param $function
     * @param $arguments
     * @return void
     * @throws \Exception
     */
    public static function __callStatic($function, $arguments)
    {
        $callback = [];
        $callback[0] = self::getBot();
        $callback[1] = $function;

        call_user_func_array($callback, $arguments);
    }
}
