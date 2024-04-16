<?php

namespace Xatbot\Bot\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Server extends Eloquent
{
    /**
     * @var array
     */
    protected $fillable = ['name'];
}
