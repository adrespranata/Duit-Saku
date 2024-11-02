<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $table = 'mst_states';
    protected $fillable = [
        'country_id',
        'name'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function cities()
    {
        return $this->hasMany(City::class);
    }
}
