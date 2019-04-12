<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;

class Feitas extends Model
{
    use BelongsToUser;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'feitases';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'ligacao_id',
        'data',
        'avaliacao',
    ];

    /**
     * @var array
     */
    protected $visible = [
        'id',
        'user_id',
        'ligacao_id',
        'data',
        'avaliacao',

        'user',
        'ligacao',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function ligacao()
    {
        return $this->belongsTo(Ligacao::class, 'ligacao_id');
    }
}
