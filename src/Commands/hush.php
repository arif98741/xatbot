<?php

$hush = function (int $who, array $message, int $type) {

    $bot = Xatbot\Bot\API\ActionAPI::getBot();

    if (!$bot->minrank($who, 'hush')) {
        return $bot->network->sendMessageAutoDetection($who, $bot->botlang('not.enough.rank'), $type);
    }

    if (!$bot->botHasPower(51)) {
        return $bot->network->sendMessageAutoDetection($who, $bot->botlang('missing.power', ['hush']), $type);
    }

    if (!isset($message[1]) || empty($message[1]) || !isset($message[2]) || empty($message[2]) ||
        !is_numeric($message[2])) {
        return $bot->network->sendMessageAutoDetection(
            $who,
            'Usage: !hush [guest/member/mod/owner] [seconds] [reason]',
            $type,
            true
        );
    }

    $rank = $message[1];
    $seconds = $message[2];

    if (isset($message[3])) {
        $reason = implode(' ', array_slice($message, 3));
    }

    switch ($rank) {
        case 'guest':
            $rank = 'g';
            break;
        case 'member':
            $rank = 'm';
            break;
        case 'mod':
            $rank = 'd';
            break;
        case 'owner':
            $rank = 'o';
            break;
        default:
            return $bot->network->sendMessageAutoDetection($who, $bot->botlang('cmd.notvalidrank'), $type);
    }

    $bot->network->sendMessage('/h' . $rank . $seconds . ' ' . $reason);
};
