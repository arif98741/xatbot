<?php

use Xatbot\Bot\API\DataAPI;

$modproof = function (int $who, array $message, int $type) {

    $bot = Xatbot\Bot\API\ActionAPI::getBot();

    if (!$bot->minrank($who, 'modproof')) {
        return $bot->network->sendMessageAutoDetection($who, $bot->botlang('not.enough.rank'), $type);
    }

    if (!DataAPI::isSetVariable('modproof')) {
        return $bot->network->sendMessageAutoDetection($who, 'I have nothing to show!', $type, true);
    }

    return $bot->network->sendMessageAutoDetection($who, DataAPI::get('modproof'), $type, true);
};
