<?php

$gameban = function (int $who, array $message, int $type) {

    $bot = Xatbot\Bot\API\ActionAPI::getBot();

    if (!$bot->minrank($who, 'gameban')) {
        return $bot->network->sendMessageAutoDetection($who, $bot->botlang('not.enough.rank'), $type);
    }

    if (!isset($message[1]) || empty($message[1]) || !isset($message[2]) || empty($message[2]) ||
        !isset($message[3]) || empty($message[3]) || !is_numeric($message[3])) {
        return $bot->network->sendMessageAutoDetection(
            $who,
            'Usage: !gameban [snake/space/match/maze/code/slot] [ID/Regname] [hours] [reason]',
            $type,
            true
        );
    }

    if (is_numeric($message[2]) && isset($bot->users[$message[2]])) {
        $user = $bot->users[$message[2]];
    } else {
        foreach ($bot->users as $id => $object) {
            if (is_object($object) && strtolower($object->getRegname()) == strtolower($message[2])) {
                $user = $object;
                break;
            }
        }
    }

    if (isset($user)) {
        $gameban = $message[1];
        $hours = $message[3];

        if (isset($message[4])) {
            $reason = implode(' ', array_slice($message, 4));
        }

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
        }

        if (!$bot->botHasPower($gamebanid)) {
            return $bot->network->sendMessageAutoDetection(
                $who,
                $bot->botlang('missing.power', [strtolower($gameban)]),
                $type
            );
        }

        $bot->network->ban($user->getID(), $hours, $reason ?? '', 'g', $gamebanid);
    } else {
        $bot->network->sendMessageAutoDetection($who, $bot->botlang('user.not.here'), $type);
    }
};
