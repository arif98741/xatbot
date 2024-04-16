<?php

namespace Xatbot\Bot;

use Xatbot\Bot\Models\Userinfo as UI;

class Userinfo
{
    /**
     * @param int $xatid
     * @param string $regname
     * @param int $chatid
     * @param string $chatname
     * @param string $packet
     */
    public function __construct(int $xatid, string $regname, int $chatid, string $chatname, string $packet)
    {
        $user = UI::where('xatid', $xatid)->first();
        if ($user === null) {
            $ui = new UI;
            $ui->xatid = $xatid;
            $ui->regname = $regname;
            $ui->chatid = $chatid;
            $ui->chatname = $chatname;
            $ui->packet = $packet;
            $ui->optout = false;
            $ui->save();
        } else {
            $user->regname = $regname;
            $user->chatid = $chatid;
            $user->chatname = $chatname;
            $user->packet = $packet;
            $user->save();
        }
    }
}
