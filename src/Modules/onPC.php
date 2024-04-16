<?php

use Xatbot\Bot\API\DataAPI;
use Xatbot\Bot\Models\Log;

$onPC = function (int $who, string $message) {

    $bot = Xatbot\Bot\API\ActionAPI::getBot();

    if (!isset($bot->users[$who])) {
        return;
    }

    $regname = $bot->users[$who]->getRegname();

    $log = new Log;
    $log->chatid = $bot->data->chatid;
    $log->chatname = $bot->data->chatname;
    $log->typemessage = 3;
    $user = (!is_null($regname) ? $regname . ' (' . $who . ')' : $who);
    $log->message = '[PC] ' . $user . ' sent: "' . utf8_encode($message) . '"';
    $log->save();

    $message = explode(' ', $message);

    if (!isset($message[0])) {
        return;
    }

    DataAPI::set('lastMessage_' . $who, time());

    if (!empty($bot->snitchlist)) {
        foreach ($bot->snitchlist as $snitch) {
            if (isset($bot->users[$snitch['xatid']])) {
                $bot->network->sendPrivateConversation($snitch['xatid'], 'PC - [' . $who . '] ' . implode($message));
            }
        }
    }

    if (DataAPI::issetVariable('kickAFK_' . $who)) {
        DataAPI::unsetVariable('kickAFK_' . $who);
        $bot->network->sendPrivateConversation($who, 'Ok (crs).');
    }

    if ($bot->data->automember == 'math' && DataAPI::issetVariable('automember_' . $who)) {
        if (DataAPI::get('automember_' . $who) == $message[0]) {
            $bot->network->sendPrivateConversation($who, 'You are now a member');
            DataAPI::unsetVariable('automember_' . $who);
            $bot->network->changeRank($who, 'member');
        } else {
            $bot->network->sendPrivateConversation($who, 'Wrong answer!');
        }
    }

    return;
};
