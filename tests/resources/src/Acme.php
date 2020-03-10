<?php

namespace Src;

use Illuminate\Database\Eloquent\Model;

class Acme extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name'
    ];
}
