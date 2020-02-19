<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BarFoo extends Model
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function fooBars()
    {
        return $this->hasMany('App\FooBar');
    }
}
