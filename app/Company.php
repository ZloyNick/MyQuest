<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    public $timestamps = false;
    protected $table = 'companies';

    protected $fillable = [
        'inn',
        'kpp',
        'address',
        'active',
        'name',
        'ogrn',
        'maintrainer',
        'name'
    ];

    function owner()
    {
        return $this->belongsTo('App\Maintrainer', 'maintrainer', 'id');
    }

}
