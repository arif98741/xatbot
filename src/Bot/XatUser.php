<?php

namespace Xatbot\Bot\Bot;

class XatUser
{
    private $id;
    private $regname;

    private $nick;
    private $avatar;
    private $home;
    private $bride;
    private $app;
    private $gameban;
    private $wasHere;

    private $flag0;
    private $aflags;
    private $qflags;

    private $login;
    private $rev;

    private $powers;
    private $maskedpowers;
    private $doubles;
    private $xats;
    private $days;

    public function __construct($packet)
    {
        $this->id = $packet['u'] ?? 0;
        $this->regname = $packet['N'] ?? null;

        $this->nick = $packet['n'] ?? null;
        $this->avatar = $packet['a'] ?? null;
        $this->home = $packet['h'] ?? null;
        $this->bride = $packet['d2'] ?? 0;
        $this->app = $packet['x'] ?? 0;
        $this->gameban = $packet['w'] ?? 0;
        $this->wasHere = isset($packet['s']);

        $this->flag0 = $packet['f'] ?? 0;
        $this->aflags = $packet['d0'] ?? 0;
        $this->qflags = $packet['q'] ?? 0;

        $this->login = $packet['cb'] ?? 0;
        $this->rev = $packet['v'] ?? 0;

        $this->setPowers($packet);
    }

    public function getID()
    {
        return $this->id;
    }

    public function getRegname()
    {
        return $this->regname;
    }

    public function getDoubles()
    {
        return (!empty($this->doubles) ? $this->doubles : null);
    }

    public function getXats()
    {
        return (!empty($this->xats) ? $this->xats : 0);
    }

    public function getDays()
    {
        return (!empty($this->days) ? $this->days : 0);
    }

    public function getPowers()
    {
        return $this->powers;
    }

    public function getMaskedPowers()
    {
        return $this->maskedpowers;
    }

    public function isStealth()
    {
        return (($this->nick[0] == '$') && ($this->isOwner() || $this->isMain()));
    }

    public function getStatus()
    {
        return substr(strstr($this->nick, '##'), 2);
    }

    public function getStatusglow()
    {
        return strstr($this->getStatus(), '#') ?? '';
    }

    public function getNick()
    {
        $pos = strpos($this->nick, '##');
        $nick = ($pos === false) ? $this->nick : strstr($this->nick, '##', true);
        $nick = preg_replace(['/\(glow[^)]+\)/', '/\(hat[^)]+\)/'], ['', ''], $nick);
        return ($this->isStealth()) ? substr($nick, 1) : $nick;
    }

    public function getNameglow()
    {
        return strstr($this->getNick(), '#') ?? '';
    }

    public function getAvatar()
    {
        return $this->avatar;
    }

    public function getHome()
    {
        return $this->home;
    }

    public function getBride()
    {
        return $this->bride;
    }

    public function wasHere()
    {
        return $this->wasHere;
    }

    public function isMarried()
    {
        return ($this->getBride() != 0);
    }

    public function isGamebanned()
    {
        return (!in_array($this->gameban, [176, 184, 0]));
    }

    public function getGameban()
    {
        return $this->gameban;
    }

    public function getRank()
    {
        return ($this->flag0 & 7);
    }

    public function isGuest()
    {
        return ($this->getRank() == 0);
    }

    public function isMain()
    {
        return ($this->getRank() == 1);
    }

    public function isOwner()
    {
        return ($this->getRank() == 4);
    }

    public function isMod()
    {
        return ($this->getRank() == 2);
    }

    public function isMember()
    {
        return ($this->getRank() == 3);
    }

    public function isBanned()
    {
        return (($this->flag0 & 1 << 4) != 0);
    }

    public function hasDays()
    {
        return ((($this->flag0 & 1 << 5) != 0) || (($this->qflags & 2) != 0));
    }

    public function isForever()
    {
        return (($this->flag0 & 1 << 6) != 0);
    }

    public function isRegistered()
    {
        return !empty($this->regname);
    }

    public function isGagged()
    {
        return (($this->flag0 & 1 << 8) != 0);
    }

    public function isSinBin()
    {
        return (($this->flag0 & 1 << 9) != 0);
    }

    public function isInvisible()
    {
        return (($this->flag0 & 1 << 10) != 0);
    }

    public function isMobile()
    {
        return !$this->onXat();
        //return (($this->flag0 & 1 << 11) != 0); "outdated mobile pawn not used anymore
    }

    public function onXat()
    {
        return (($this->qflags & 1) != 0);
    }

    public function isBannished()
    {
        return (($this->flag0 & 1 << 12) != 0);
    }

    public function isBot()
    {
        return (($this->flag0 & 1 << 13) != 0);
    }

    public function isAway()
    {
        return (($this->flag0 & 1 << 14) != 0);
    }

    public function isDunced()
    {
        return (($this->flag0 & 1 << 15) != 0);
    }

    public function isTyping()
    {
        return (($this->flag0 & 1 << 16) != 0);
    }

    public function isZipped()
    {
        return (($this->flag0 & 1 << 17) != 0);
    }

    public function isReverseBanned()
    {
        return (($this->flag0 & 1 << 17) != 0);
    }

    public function isBadged()
    {
        return (($this->flag0 & 1 << 18) != 0);
    }

    public function isNaughty()
    {
        return (($this->flag0 & 1 << 19) != 0);
    }

    public function isYellowCarded()
    {
        return (($this->flag0 & 1 << 20) != 0);
    }

    public function isBFF()
    {
        return (($this->aflags & 1) != 0);
    }

    public function isRedCarded()
    {
        return (($this->aflags & 1 << 21) != 0);
    }

    public function hasGifts()
    {
        return (($this->aflags & 1 << 24) != 0);
    }

    public function isCelebrity()
    {
        return (($this->aflags & 1 << 71) != 0);
    }

    public function setPowers($packet)
    {
        for ($i = 0; $i < XatVariables::getMaxPowerIndex(); $i++) {
            $this->powers[$i] = $packet['p' . $i] ?? 0;
        }
    }

    public function setMaskedPowers($packet)
    {
        for ($i = 0; $i < XatVariables::getMaxPowerIndex(); $i++) {
            $this->maskedpowers[$i] = isset($packet['p' . $i]) ? $packet['p' . $i] - $this->powers[$i] : 0;
        }
    }

    public function setDoubles($info)
    {
        $this->doubles = $info;
    }

    public function setXats($xats)
    {
        $this->xats = (int)$xats;
    }

    public function setDays($days)
    {
        $this->days = (int)$days;
    }

    public function hasPower($id, $masked = false)
    {
        if (!$this->hasDays()) {
            return false;
        }

        $id = (int)$id;
        $index = (int)($id / 32);
        $bit = (int)($id % 32);

        if ($masked) {
            return (isset($this->maskedpowers[$index]) && ($this->maskedpowers[$index] & (1 << $bit)));
        }

        return (isset($this->powers[$index]) && ($this->powers[$index] & (1 << $bit)));
    }

    public function getLoginTimestamp()
    {
        return $this->login;
    }

    public function getFlag()
    {
        return $this->flag0;
    }

    public function getRev()
    {
        return $this->rev;
    }
}
