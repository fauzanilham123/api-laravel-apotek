<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class drug extends Model
{
    //
    use HasFactory;

    protected $fillable =  [
        'id',
        'kode_obat',
        'nama_obat',
        'image',
        'expired_date',
        'jumlah',
        'harga',
        'flag'
    ];
    protected $casts = [
        'expired_date' => 'date:Y-m-d', // Mengatur format penyimpanan dan pengambilan
    ];

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn($image) => url('/storage/posts/' . $image),
        );
    }
}