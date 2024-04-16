<?php

require_once 'vendor/autoload.php';

use Xatbot\Bot\Server;
use Xatbot\Bot\Models;

$server = new Server($argv[1]);
$server->handle();
