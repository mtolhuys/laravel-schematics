<?php

namespace Mtolhuys\LaravelSchematics\Models;

use Illuminate\Database\Eloquent\Model;

class Migration extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'migration',
        'batch',
    ];

    /**
     * Possible methods currently only used by RuleParser
     * WARNING: be aware of word length vs. word matching
     * f.e. 'date' <-> 'dateTime'
     *
     * @var array
     */
    public static $methods = [
        'rememberToken',
        'softDeletes',
        'softDeletesTz',
        'bigIncrements',
        'bigInteger',
        'dropColumn',
        'ipAddress',
        'macAddress',
        'mediumInteger',
        'mediumIncrements',
        'renameColumn',
        'smallIncrements',
        'timestampTz',
        'timestamp',
        'smallInteger',
        'tinyIncrements',
        'tinyInteger',
        'unsignedInteger',
        'increments',
        'dateTimeTz',
        'dateTime',
        'longText',
        'integer',
        'boolean',
        'decimal',
        'date',
        'enum',
        'geometry',
        'jsonb',
        'json',
        'point',
        'polygon',
        'string',
        'text',
        'time',
        'unsigned',
        'uuid',
        'year',
    ];
}
