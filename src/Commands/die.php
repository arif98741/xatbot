<?php

$die = function (int $who, array $message, int $type) {

    $bot = Xatbot\Bot\API\ActionAPI::getBot();

    if (!$bot->minrank($who, 'die')) {
        return $bot->network->sendMessageAutoDetection($who, $bot->botlang('not.enough.rank'), $type);
    }

    $bot->network->sendMessageAutoDetection($who, $bot->botlang('cmd.die'), $type);
    $bot->stopped = true;
};
