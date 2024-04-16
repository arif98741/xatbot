<?php

$onChatInfo = function (array $array) {

    $bot = Xatbot\Bot\API\ActionAPI::getBot();

    $rankA = [0 => 'Guest', 1 => 'Main', 2 => 'Moderator', 3 => 'Member', 4 => 'Owner', 5 => 'Guest'];
    $info = explode(';=', $array['b']);

    $bot->chatInfo['background'] = explode('#', $info[0])[0];
    $bot->chatInfo['tabbedChat'] = @$info[1];
    $bot->chatInfo['tabbedChatID'] = @$info[2];
    $bot->chatInfo['language'] = $info[3] ?? 'en';
    $bot->chatInfo['radio'] = isset($info[4]) ? str_replace(';', '', $info[4]) : '';
    $bot->chatInfo['buttons'] = $info[5] ?? 'None';
    $bot->chatInfo['bot'] = $array['B'] ?? $bot->network->logininfo['i'];
    $bot->chatInfo['rank'] = isset($array['r']) && isset($rankA[$array['r']]) ? $rankA[$array['r']] : 'Guest';

    $chats_allowed = [
        164872174,
        220116374,
        15363891,
        160324526,
        6
    ];

    if ($bot->chatInfo['bot'] != $bot->network->logininfo['i'] && !in_array($bot->data->chatid, $chats_allowed)) {
        $bot->network->sendMessage('You need to assign (bot) power on your chat with id 10101. Restart me once it\'s done. (bye)');
        $bot->stopped = true;
    }
};
