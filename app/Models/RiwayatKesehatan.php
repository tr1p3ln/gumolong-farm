<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    protected $table      = 'medical_record';
    protected $primaryKey = 'rekam_id';

    public    $incrementing = true;
    protected $keyType      = 'int';

    protected $fillable = [
        'ear_tag_id',
        'tanggal_sakit',
        'gejala',
        'diagnosa',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal_sakit' => 'date',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];

    // ── Relasi dengan PemakaianObat ────────────────────────────────
    public function pemakaianObat()
    {
        return $this->hasMany(PemakaianObat::class, 'rekam_id', 'rekam_id');
    }

    // ── Relasi dengan Domba (jika ada) ────────────────────────────
    public function domba()
    {
        return $this->belongsTo(Domba::class, 'ear_tag_id', 'ear_tag_id');
    }
}
