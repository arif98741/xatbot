<?php

use Illuminate\Database\Capsule\Manager as Capsule;

$userinfo = function (int $who, array $message, int $type) {

    $bot = Xatbot\Bot\API\ActionAPI::getBot();

    if (!$bot->minrank($who, 'userinfo')) {
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
    } elseif (in_array(strtolower($message[1]), ['on', 'off'])) {
        switch (strtolower($message[1])) {
            case 'on':
                Capsule::table('userinfo')->where('xatid', $who)->update(['optout' => 0]);
                return $bot->network->sendMessageAutoDetection(
                    $who,
                    $bot->botlang('user.haveoptedinto'),
                    $type
                );
                break;

            case 'off':
                Capsule::table('userinfo')->where('xatid', $who)->update(['optout' => 1]);
                return $bot->network->sendMessageAutoDetection(
                    $who,
                    $bot->botlang('user.haveoptoutwithsuccess'),
                    $type
                );
                break;
        }
    } else {
        $info = Capsule::table('userinfo')
            ->whereRaw('LOWER(regname) = ?', [strtolower($message[1])])
            ->orderBy('updated_at', 'desc')
            ->get()
            ->toArray();
    }

    if (!empty($info)) {
        $info = $info[0];
        if ($info->optout !== true) {
            return $bot->network->sendMessageAutoDetection(
                $who,
                xatbot\Bot\XatVariables::getConfig()['website_url'] . '/panel/userinfo/' . $info->regname,
                $type
            );
        } else {
            return $bot->network->sendMessageAutoDetection(
                $who,
                $bot->botlang('user.optoutuserinfo', [$info->regname]),
                $type
            );
        }
    } else {
        return $bot->network->sendMessageAutoDetection($who, $bot->botlang('user.notindatabase'), $type);
    }
};
