<?php

use Xatbot\Bot\API\ActionAPI;
use Xatbot\Bot\API\DataAPI;

$steam = function (int $who, array $message, int $type) {

    $bot = ActionAPI::getBot();

    if (!$bot->minrank($who, 'steam')) {
        return $bot->network->sendMessageAutoDetection($who, $bot->botlang('not.enough.rank'), $type);
    }

    if (!DataAPI::issetVariable('steam_' . $who)) {
        return $bot->network->sendMessageAutoDetection($who, $bot->botlang('cmd.steam.pleaserefresh'), $type);
    }

    return $bot->steam($who, $type);
};
