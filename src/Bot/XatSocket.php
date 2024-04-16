<?php

namespace Xatbot\Bot\Bot;

use Xatbot\Bot\Logger;

class XatSocket
{
    private $socket;
    private $buffer;
    private $botid;
    public $connected = false;

    public function __construct($botid)
    {
        $this->botid = $botid;
    }

    public function __destruct()
    {
        $this->disconnect();
    }

    public function connect($ip, $port, $timeout)
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        if ($this->socket === false) {
            return false;
        }

        socket_set_nonblock($this->socket);
        $time = microtime(true) + $timeout;
        socket_connect($this->socket, $ip, $port);
        socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO, ['sec' => 5, 'usec' => 0]);

        do {
            if (@socket_getpeername($this->socket, $ip, $port) && $ip != '0.0.0.0') {
                $this->connected = true;
                return $this->isConnected();
            }
        } while (microtime(true) < $time);

        return false;
    }

    public function disconnect()
    {
        $this->socket = null;
        $this->connected = false;
    }

    public function read($force = false)
    {
        if (!$this->isConnected()) {
            $this->disconnect();
            return false;
        }

        $status = @socket_get_status($this->socket);
        if (isset($status['timed_out'])) {
            $this->disconnect();
            return false;
        }

        if ($force === true) {
            socket_set_block($this->socket);
        }

        socket_clear_error($this->socket);

        $timeout = time() + 10;

        do {
            $packet = socket_read($this->socket, 1460);

            if ($packet === false && (socket_last_error($this->socket) !== 0)
                && (socket_last_error($this->socket) !== 11)
            ) {
                $this->disconnect();
                return false;
            }

            if (!$this->socket) {
                $this->disconnect();
                return false;
            }

            if (socket_last_error($this->socket) !== 0 && (socket_last_error($this->socket) !== 11)) {
                $this->disconnect();
                return false;
            }

            if ($timeout < time()) {
                $this->disconnect();
                return false;
            }

            $this->buffer .= $packet;
        } while ($force && strpos($this->buffer, chr(0x00)) === false);

        if ($force === true) {
            socket_set_nonblock($this->socket);
        }

        return $this->getPacket();
    }

    public function write($node = null, $elements = [], $sanitize = true)
    {
        if (!$this->isConnected()) {
            $this->disconnect();
            return false;
        }

        $packet = $this->forgePacket($node, $elements, $sanitize);

        if (socket_write($this->socket, $packet . chr(0x00)) === false) {
            $this->disconnect();
            return false;
        } else {
            Logger::getLogger()->info('[' . $this->botid . '] --> ' . $packet);
            return true;
        }
    }

    private function getPacket()
    {
        $pos = strpos($this->buffer, chr(0x00));
        if ($pos === false) {
            return;
        }

        $packet = substr($this->buffer, 0, $pos);
        $this->buffer = substr($this->buffer, $pos + 1);

        Logger::getLogger()->info('[' . $this->botid . '] <-- ' . $packet);

        return $this->parsePacket($packet);
    }

    private function parsePacket($string)
    {
        $node = null;
        $elements = [];

        // Removing < />
        $string = trim($string);

        if (($string[0] != '<') || (substr($string, -2) != '/>')) {
            throw new Exception('Corrupted packets.');
        }

        $string = substr($string, 1, -2);

        // Getting the node
        $pos = strpos($string, ' ');
        $node = ($pos === false) ? $string : substr($string, 0, $pos);

        $n = preg_match_all('! ([^ =]+(?:="[^"]+")?)!', $string, $matches);

        for ($i = 0; $i < $n; $i++) {
            $pos = strpos($matches[1][$i], '=');

            if ($pos === false) {
                $elements[] = $matches[1][$i];
            } else {
                $elements[substr($matches[1][$i], 0, $pos)]
                    = $this->unsanitize(substr($matches[1][$i], $pos + 2, -1));
            }
        }

        return ['node' => $node, 'elements' => $elements];
    }

    private function forgePacket($node = null, $elements = [], $sanitize = true)
    {
        $counter = 0;
        $packet = '<' . $node . ' ';

        foreach ($elements as $name => $value) {
            if (is_int($name) && ($name == $counter)) {
                $packet .= $value;
                $counter++;
            } else {
                $packet .= $name . '=';
                $packet .= '"' . (($sanitize === true) ? $this->sanitize($value) : $value) . '"';
            }

            $packet .= ' ';
        }

        $packet .= '/>';

        return $packet;
    }

    public function sanitize($str)
    {
        $str = trim($str);

        $str = str_replace('&', '&amp;', $str);
        $str = str_replace('"', '&quot;', $str);
        $str = str_replace("'", '&apos;', $str);
        $str = str_replace('<', '&lt;', $str);
        $str = str_replace('>', "\xCB\x83", $str);

        return $str;
    }

    public function unsanitize($str)
    {
        $str = str_replace(chr(0xCB) . chr(0x83), '>', $str);
        $str = str_replace('&lt;', '<', $str);
        $str = str_replace('&apos;', "'", $str);
        $str = str_replace('&quot;', '"', $str);
        $str = str_replace('&amp;', '&', $str);

        return $str;
    }

    public function isConnected()
    {
        return ($this->connected);
    }
}
