<?php

$guestself = function (int $who, array $message, int $type) {

    $bot = Xatbot\Bot\API\ActionAPI::getBot();

    if (!$bot->minrank($who, 'guestself')) {
        return $bot->network->sendMessageAutoDetection($who, $bot->botlang('not.enough.rank'), $type);
    }

    if (!$bot->botHasPower(32)) {
        return $bot->network->sendMessageAutoDetection($who, $bot->botlang('missing.power', ['guestself']), $type);
    }

    $bot->network->sendMessage('/g');
    $bot->data->chatpw = '123';
    $bot->data->save();
    $bot->network->sendMessageAutoDetection($who, $bot->botlang('cmd.guestself'), $type, true);
};
