<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'mst_countries';
    protected $fillable = [
        'code',
        'name',
        'phonecode',
    ];

    public function states()
    {
        return $this->hasMany(State::class);
    }
}
