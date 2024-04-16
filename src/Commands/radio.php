<?php

use Xatbot\Bot\API\DataAPI;

$radio = function (int $who, array $message, int $type) {

    $bot = Xatbot\Bot\API\ActionAPI::getBot();

    if (!$bot->minrank($who, 'radio')) {
        return $bot->network->sendMessageAutoDetection($who, $bot->botlang('not.enough.rank'), $type);
    }

    if (DataAPI::isSetVariable('radio')) {
        $infos = DataAPI::get('radio');
        if ($infos['lastCheck'] > time()) {
            return $bot->network->sendMessageAutoDetection(
                $who,
                $bot->botlang('cmd.radio.listeningto', [
                    $infos['song'],
                    $infos['listeners'],
                    $infos['max']
                ]),
                $type
            );
        }
    }

    $song = $bot->getCurrentSong();

    if ($song == false) {
        return $bot->network->sendMessageAutoDetection($who, $bot->botlang('cmd.radio.error'), $type);
    }

    DataAPI::set('radio', $song);

    return $bot->network->sendMessageAutoDetection(
        $who,
        $bot->botlang('cmd.radio.listeningto', [
            $song['song'],
            $song['listeners'],
            $song['max']
        ]),
        $type
    );
};
