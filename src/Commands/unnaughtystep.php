<?php

$unnaughtystep = function (int $who, array $message, int $type) {

    $bot = Xatbot\Bot\API\ActionAPI::getBot();

    if (!$bot->minrank($who, 'unnaughtystep')) {
        return $bot->network->sendMessageAutoDetection($who, $bot->botlang('not.enough.rank'), $type);
    }

    if (!$bot->botHasPower(284)) {
        return $bot->network->sendMessageAutoDetection($who, $bot->botlang('missing.power', ['naughtystep']), $type);
    }

    if (!isset($message[1]) || empty($message[1])) {
        return $bot->network->sendMessageAutoDetection($who, 'Usage: !unnaughtystep [regname/xatid]', $type, true);
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
        if (!$user->isNaughty()) {
            return $bot->network->sendMessageAutoDetection($who, $bot->botlang('cmd.unbadge.notnaughty'), $type);
        }

        $bot->network->ban($user->getID(), 0, $reason ?? '', 'gn');
        $bot->network->sendMessageAutoDetection($who, $bot->botlang('cmd.unbadge.nowunaughty'), $type);
    } else {
        $bot->network->sendMessageAutoDetection($who, $bot->botlang('user.not.here'), $type);
    }
};
