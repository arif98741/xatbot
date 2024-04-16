<?php

$unyellowcard = function (int $who, array $message, int $type) {

    $bot = Xatbot\Bot\API\ActionAPI::getBot();

    if (!$bot->minrank($who, 'unyellowcard')) {
        return $bot->network->sendMessageAutoDetection($who, $bot->botlang('not.enough.rank'), $type);
    }

    if (!$bot->botHasPower(292)) {
        return $bot->network->sendMessageAutoDetection($who, $bot->botlang('missing.power', ['yellowcard']), $type);
    }

    if (!isset($message[1]) || empty($message[1])) {
        return $bot->network->sendMessageAutoDetection($who, 'Usage: !unyellowcard [regname/xatid]', $type, true);
    }

    if (is_numeric($message[1]) && isset($bot->users[$message[1]])) {
        $user = $bot->users[$message[1]];
    } else {
        foreach ($bot->users as $id => $object) {
            if (is_object($object)) {
                if (strtolower($object->getRegname()) == strtolower($message[1])) {
                    $user = $object;
                    break;
                }
            }
        }
    }

    if (isset($user)) {
        if (!$user->isYellowCarded()) {
            return $bot->network->sendMessageAutoDetection($who, $bot->botlang('cmd.unbadge.notyellow'), $type);
        }

        $bot->network->ban($user->getID(), 0, $reason ?? '', 'gy');
        $bot->network->sendMessageAutoDetection($who, $bot->botlang('cmd.unbadge.nowunyellowcarded'), $type);
    } else {
        $bot->network->sendMessageAutoDetection($who, $bot->botlang('user.not.here'), $type);
    }
};
