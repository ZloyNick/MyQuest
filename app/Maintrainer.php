<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintrainer extends Model
{

    public $timestamps = false;
    protected $table = 'maintrainers';

    protected $fillable = [
        'name',
        'role'
    ];

}
