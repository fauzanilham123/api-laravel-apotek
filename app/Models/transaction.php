<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class transaction extends Model
{
    //
    use HasFactory;
    protected $fillable = [
        'no',
        'date',
        'nama_kasir',
        'total_bayar',
        'id_user',
        'id_drug',
        'id_recipe',
        'flag',
    ];
}