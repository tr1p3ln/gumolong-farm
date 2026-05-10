<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PemberianPakan extends Model
{
    protected $table = 'pemberian_pakan';
    protected $primaryKey = 'pemberian_id';

    protected $fillable = [
        'pakan_id',
        'ear_tag_id',
        'user_id',
        'tanggal_pemberian',
        'sesi',
        'jumlah_gram',
        'satuan',
        'sisa_stok',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_pemberian' => 'datetime',
        'jumlah_gram'       => 'float',
    ];

    public $timestamps = true;

    public function getJumlahAttribute()
    {
        return $this->jumlah_gram / 1000;
    }

    // ---------------------------------------------------------------- RELASI

    public function pakanStok()
    {
        return $this->belongsTo(PakanStok::class, 'pakan_id', 'pakan_id');
    }

    public function domba()
    {
        return $this->belongsTo(Domba::class, 'ear_tag_id', 'ear_tag_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userr_id', 'user_id');
    }
}