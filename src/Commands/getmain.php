<?php

$getmain = function (int $who, array $message, int $type) {

    $bot = Xatbot\Bot\API\ActionAPI::getBot();

    if (!$bot->minrank($who, 'getmain')) {
        return $bot->network->sendMessageAutoDetection($who, $bot->botlang('not.enough.rank'), $type);
    }

    if ($type != 3) {
        return $bot->network->sendMessageAutoDetection($who, $bot->botlang('cmd.doinpc'), $type);
    }

    if (!isset($message[1]) || empty($message[1])) {
        return $bot->network->sendPrivateConversation($who, 'Usage: !getmain [chatpassword]');
    }

    $group = $bot->getChatName($bot->data->chatid);

    $POST['GroupName'] = $group;
    $POST['password'] = $message[1];
    $POST['SubmitPass'] = 'Submit';

    $stream = [
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/x-www-form-urlencoded',
            'content' => http_build_query($POST)
        ]
    ];

    $res = file_get_contents('https://xat.com/web_gear/chat/editgroup.php', false, stream_context_create($stream));

    if (strpos($res, '**<span data-localize=buy.wrongpassword>Wrong password</span>')) {
        return $bot->network->sendMessageAutoDetection($who, $bot->botlang('cmd.wrongpass'), $type);
    } elseif (strpos($res, '**Error. Try again in 10 minutes.**')) {
        return $bot->network->sendMessageAutoDetection($who, $bot->botlang('cmd.getmain.tryin10'), $type);
    }

    $pw = $bot->stribet($res, '<input name="pw" type="hidden" value="', '">');
    if (!is_numeric($pw)) {
        return $bot->network->sendPrivateConversation($who, $bot->botlang('cmd.getmain.cantget'));
    }

    $bot->data->chatpw = $pw;
    $bot->data->save();
    $bot->network->sendPrivateConversation($who, $bot->botlang('cmd.getmain.gotmain'));
    $bot->refresh();
};
