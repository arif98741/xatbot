<?php

$countdown = function (int $who, array $message, int $type) {

    $bot = Xatbot\Bot\API\ActionAPI::getBot();

    if (!$bot->minrank($who, 'countdown')) {
        return $bot->network->sendMessageAutoDetection($who, $bot->botlang('not.enough.rank'), $type);
    }

    $releaseTime = Xatbot\Bot\Bot\XatVariables::getReleaseTime();

    if ($releaseTime > 1) {
        $message = $bot->botlang('cmd.countdown.releasein', [gmdate("H:i:s", $releaseTime - time())]);
    } else {
        $message = $bot->botlang('cmd.countdown.nocountdown');
    }

    $bot->network->sendMessageAutoDetection($who, $message, $type);
};
