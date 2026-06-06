<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateTugasRutin extends Model
{
    use HasFactory;

    protected $table = 'template_tugas_rutin';

    protected $fillable = [
        'judul',
        'deskripsi',
        'kandang_id',
        'user_id',
        'waktu_default',
        'prioritas',
        'is_active',
    ];

    protected $casts = [
        'is_active'     => 'boolean',
        'waktu_default' => 'datetime:H:i',
    ];

    public function kandang()
    {
        return $this->belongsTo(Kandang::class, 'kandang_id', 'kandang_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
