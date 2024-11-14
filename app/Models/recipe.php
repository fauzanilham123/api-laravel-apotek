<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class recipe extends Model
{
    //
    use HasFactory;
    protected $fillable = [
        'no',
        'date',
        'nama_dokter',
        'nama_pasien',
        'id_obat',
        'jumlah_obat',
        'flag',
    ];

    protected $casts = [
        'date' => 'date:Y-m-d', // Mengatur format penyimpanan dan pengambilan
    ];
    public function obat()
    {
        return $this->belongsTo(drug::class, 'id_obat');
    }
}