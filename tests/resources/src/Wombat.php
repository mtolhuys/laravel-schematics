<?php

namespace Src;

use Illuminate\Database\Eloquent\Model;

class Wombat extends Model
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function acmes()
    {
        return $this->belongsTo('Src\Acme');
    }
}
