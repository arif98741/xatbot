<?php

$dunce = function (int $who, array $message, int $type) {

    $bot = Xatbot\Bot\API\ActionAPI::getBot();

    if (!$bot->minrank($who, 'dunce')) {
        return $bot->network->sendMessageAutoDetection($who, $bot->botlang('not.enough.rank'), $type);
    }

    if (!$bot->botHasPower(158)) {
        return $bot->network->sendMessageAutoDetection($who, $bot->botlang('missing.power', ['dunce']), $type);
    }

    if (!isset($message[1]) || empty($message[1])) {
        return $bot->network->sendMessageAutoDetection($who, 'Usage: !dunce [regname/xatid] [reason]', $type, true);
    }

    if (is_numeric($message[1]) && isset($bot->users[$message[1]])) {
        $user = $bot->users[$message[1]];
    } else {
        foreach ($bot->users as $id => $object) {
            if (is_object($object) && strtolower($object->getRegname()) == strtolower($message[1])) {
                $user = $object;
                break;
            }
        }
    }

    if (isset($user)) {
        if ($user->isDunced()) {
            return $bot->network->sendMessageAutoDetection($who, $bot->botlang('user.already', ['dunced']), $type);
        }

        if (isset($message[2])) {
            $reason = implode(' ', array_slice($message, 2));
        }

        $bot->network->ban($user->getID(), 0, $reason ?? '', 'gd');
    } else {
        $bot->network->sendMessageAutoDetection($who, $bot->botlang('user.not.here'), $type);
    }
};
