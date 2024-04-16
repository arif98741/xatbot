<?php

$onDup = function () {

    $bot = Xatbot\Bot\API\ActionAPI::getBot();
    $bot->stop();
};
