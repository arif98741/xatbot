<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Xatbot\Bot\Bot\XatVariables;

$allmissing = function (int $who, array $message, int $type) {

    $bot = Xatbot\Bot\API\ActionAPI::getBot();

    if (!$bot->minrank($who, 'allmissing')) {
        return $bot->network->sendMessageAutoDetection($who, $bot->botlang('not.enough.rank'), $type);
    }

    if (empty($message[1])) {
        $message[1] = $who;
    }

    $info = null;
    if (is_numeric($message[1])) {
        $message[1] = (int)$message[1];

        if ($message[1] == 9223372036854775807) {
            return $bot->network->sendMessageAutoDetection($who, 'I am in a 64-bit environment.', $type, true);
        }

        $info = Capsule::table('userinfo')
            ->where('xatid', $message[1])
            ->orderBy('updated_at', 'desc')
            ->get()
            ->toArray();
    } else {
        $info = Capsule::table('userinfo')
            ->whereRaw('LOWER(regname) = ?', [strtolower($message[1])])
            ->orderBy('updated_at', 'desc')
            ->get()
            ->toArray();
    }

    if (!empty($info)) {
        $info = $info[0];
        $message = $bot->botlang('cmd.allmissing.canbeseen', [$info->regname]);
        return $bot->network->sendMessageAutoDetection(
            $who,
            $message . XatVariables::getConfig()['website_url'] . '/panel/allmissing/' . $info->regname,
            $type
        );
    } else {
        return $bot->network->sendMessageAutoDetection($who, $bot->botlang('user.notindatabase'), $type);
    }
};
