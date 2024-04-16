<?php

$sinbin = function (int $who, array $message, int $type) {

    $bot = Xatbot\Bot\API\ActionAPI::getBot();

    if (!$bot->minrank($who, 'sinbin')) {
        return $bot->network->sendMessageAutoDetection($who, $bot->botlang('not.enough.rank'), $type);
    }

    if (!$bot->botHasPower(33)) {
        return $bot->network->sendMessageAutoDetection($who, $bot->botlang('missing.power', ['sinbin']), $type);
    }

    if (!isset($message[1]) || empty($message[1]) || !isset($message[2]) || empty($message[2]) ||
        !is_numeric($message[2])) {
        return $bot->network->sendMessageAutoDetection($who, 'Usage: !sinbin [regname/xatid] [hours]', $type, true);
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
        if (!$user->isMod()) {
            return $bot->network->sendMessageAutoDetection($who, $bot->botlang('cmd.sinbin.notmod'), $type);
        }

        $hours = $message[2];
        $bot->network->sendPrivateConversation($user->getID(), '/n' . $hours);
    } else {
        $bot->network->sendMessageAutoDetection($who, $bot->botlang('user.not.here'), $type);
    }
};
