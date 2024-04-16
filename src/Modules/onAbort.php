<?php

$onAbort = function (array $array) {

    $bot = Xatbot\Bot\API\ActionAPI::getBot();
    // for the time being
    $bot->network->reconnect();
};
