<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ligacao extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'ligacaos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'texto',
        'data',
        'status',
    ];

    /**
     * @var array
     */
    protected $visible = [
        'id',
        'texto',
        'data',
        'status',

        'users',
        'feitas'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'feitases', 'ligacao_id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function feitas()
    {
        return $this->hasOne(Feitas::class);
    }
}
