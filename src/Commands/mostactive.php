<?php

use Xatbot\Bot\API\DataAPI;

$mostactive = function (int $who, array $message, int $type) {

    $bot = Xatbot\Bot\API\ActionAPI::getBot();

    if (!$bot->minrank($who, 'mostactive')) {
        return $bot->network->sendMessageAutoDetection($who, $bot->botlang('not.enough.rank'), $type);
    }

    $most = ['user' => null, 'time' => 0];

    foreach ($bot->users as $user) {
        if (!is_object($user) || !DataAPI::isSetVariable('active_' . $user->getID())) {
            continue;
        }

        $userTime = DataAPI::get('active_' . $user->getID());

        if ($most['time'] == 0 || $userTime < $most['time']) {
            $most = ['user' => $user, 'time' => $userTime];
        }
    }

    $displayName = $most['user']->isRegistered() ?
        $most['user']->getRegname() . '(' . $most['user']->getID() . ')' :
        $most['user']->getID();

    $bot->network->sendMessageAutoDetection(
        $who,
        $bot->botlang('cmd.mostactive', [$displayName, $bot->secondsToTime(time() - $userTime)]),
        $type
    );
};
