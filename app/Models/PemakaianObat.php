<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PemakaianObat extends Model
{
    protected $table      = 'pemakaian_obat';
    protected $primaryKey = 'pemakaian_id'; 

    public    $incrementing = true;
    protected $keyType      = 'int';

    protected $fillable = [
        'rekam_id',
        'obat_id',
        'jumlah',
        'cara_pemberian',
        'catatan',
        'tanggal_pakai',
    ];

    protected $casts = [
        'tanggal_pakai' => 'date',
        'jumlah'        => 'decimal:2',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];

    // ── Relasi dengan ObatVaksin ───────────────────────────────────
    public function obat()
    {
        return $this->belongsTo(ObatVaksin::class, 'obat_id', 'obat_id');
    }

    // ── Relasi dengan MedicalRecord ────────────────────────────────
    public function rekam()
    {
        return $this->belongsTo(MedicalRecord::class, 'rekam_id', 'rekam_id');
    }

    // ── Format tanggal pakai ───────────────────────────────────────
    public function getTanggalPakaiFormattedAttribute(): string
    {
        return $this->tanggal_pakai?->format('d M Y') ?? '—';
    }
}