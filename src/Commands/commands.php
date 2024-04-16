<?php

use Xatbot\Bot\Bot\XatVariables;

$commands = function (int $who, array $message, int $type) {

    $bot = Xatbot\Bot\API\ActionAPI::getBot();

    if (!$bot->minrank($who, 'commands')) {
        return $bot->network->sendMessageAutoDetection($who, $bot->botlang('not.enough.rank'), $type);
    }

    $bot->network->sendMessageAutoDetection(
        $who,
        XatVariables::getConfig()['website_url'] . '/panel/commands/' . $bot->data->id,
        $type
    );
};
