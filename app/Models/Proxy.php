<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class Proxy extends Model
{
    use AsSource;

    public $timestamps = [];

    const UPDATED_AT = null;
    const CREATED_AT = null;

    /**
     * @var array
     */
    protected $fillable = [
        'uri',
    ];
}
