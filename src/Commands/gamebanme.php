<?php

$gamebanme = function (int $who, array $message, int $type) {

    $bot = Xatbot\Bot\API\ActionAPI::getBot();

    if (!$bot->minrank($who, 'gamebanme')) {
        return $bot->network->sendMessageAutoDetection($who, $bot->botlang('not.enough.rank'), $type);
    }

    if (!isset($message[1]) || empty($message[1]) || !isset($message[2]) || empty($message[2]) ||
        !is_numeric($message[2])) {
        return $bot->network->sendMessageAutoDetection(
            $who,
            'Usage: !gamebanme [snake/space/match/maze/code/slot] [hours]',
            $type,
            true
        );
    }

    $gameban = $message[1];
    $hours = $message[2];

    switch (strtolower($gameban)) {
        case 'snake':
        case 'snakeban':
            $gamebanid = 134;
            break;

        case 'space':
        case 'spaceban':
            $gamebanid = 136;
            break;

        case 'match':
        case 'matchban':
            $gamebanid = 140;
            break;

        case 'maze':
        case 'mazeban':
            $gamebanid = 152;
            break;

        case 'code':
        case 'codeban':
            $gamebanid = 162;
            break;

        case 'slot':
        case 'slotban':
            $gamebanid = 236;
            break;

        default:
            return $bot->network->sendMessageAutoDetection($who, $bot->botlang('gameban.notvalid'), $type);
            break;
    }

    if (!$bot->botHasPower($gamebanid)) {
        return $bot->network->sendMessageAutoDetection(
            $who,
            $bot->botlang('missing.power', [strtolower($gameban)]),
            $type
        );
    }

    $bot->network->ban($who, $hours, 'Requested', 'g', $gamebanid);
};
