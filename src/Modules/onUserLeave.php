<?php

use Xatbot\Bot\API\DataAPI;
use Xatbot\Bot\Models\UserEvents;

$onUserLeave = function (int $who) {

    $bot = Xatbot\Bot\API\ActionAPI::getBot();

    if ($who >= 1900000000) {
        return;
    }

    // Auto gamebot?
    if ($who == 804) {
        if (DataAPI::issetVariable('bot') && (DataAPI::get('bot') == true)) {
            $bot->network->sendMessage('!bot');
            usleep(500000);
            $bot->network->sendMessage('!start');
        }
    }

    unset($bot->users[$who]);

    if (DataAPI::issetVariable('away_' . $who)) {
        DataAPI::unsetVariable('away_' . $who);
    }

    if (DataAPI::issetVariable('joined_' . $who)) {
        DataAPI::unsetVariable('joined_' . $who);
    }

    if (DataAPI::issetVariable('spotify_' . $who)) {
        DataAPI::unsetVariable('spotify_' . $who);
    }

    if (DataAPI::issetVariable('steam_' . $who)) {
        DataAPI::unsetVariable('steam_' . $who);
    }

    if (DataAPI::issetVariable('boards_' . $who)) {
        DataAPI::unsetVariable('boards_' . $who);
    }

    if (DataAPI::issetVariable('botstat_' . $who)) {
        DataAPI::unsetVariable('botstat_' . $who);
    }

    if (DataAPI::issetVariable('kickAFK_' . $who)) {
        DataAPI::unsetVariable('kickAFK_' . $who);
    }

    if (DataAPI::issetVariable('lastMessage_' . $who)) {
        DataAPI::unsetVariable('lastMessage_' . $who);
    }

    if (DataAPI::issetVariable('isAutotemp_' . $who)) {
        DataAPI::unsetVariable('isAutotemp_' . $who);
    }

    if (DataAPI::issetVariable('hangman_' . $who)) {
        DataAPI::unsetVariable('hangman_' . $who);
    }

    if (!DataAPI::issetVariable('moderated_' . $who)) {
        DataAPI::unsetVariable('moderated_' . $who);
    }

    if (DataAPI::issetVariable('userEvent_' . $who)) {
        $event = DataAPI::get('userEvent_' . $who);
        $event['left_at'] = date('Y/m/d H:i:s', time());

        $UserEvents = new UserEvents;
        foreach ($event as $key => $value) {
            $UserEvents->$key = $value;
        }
        $UserEvents->save();
        DataAPI::unsetVariable('userEvent_' . $who);
    }

    DataAPI::set('left_' . $who, time());

    return;
};
