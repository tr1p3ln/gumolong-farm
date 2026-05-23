<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObatVaksin extends Model
{
    protected $table      = 'obat_vaksin';
    protected $primaryKey = 'obat_id';

    public    $incrementing = true;
    protected $keyType      = 'int';

    protected $fillable = [
        'nama_obat',
        'tipe',
        'satuan',
        'stok',
        'stok_minimum',
        'harga_beli',
        'tanggal_expired',
        'interval_vaksinasi',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_expired'    => 'date',
        'stok'               => 'integer',
        'stok_minimum'       => 'integer',
        'harga_beli'         => 'decimal:2',
        'interval_vaksinasi' => 'integer',
    ];

    // ── Prefix per tipe ──────────────────────────────────────────
    public static function prefixForTipe(string $tipe): string
    {
        return match($tipe) {
            'vaksin'  => 'VAK',
            'vitamin' => 'VIT',
            default   => 'OBT',
        };
    }

    // ── Formatted ID ─────────────────────────────────────────────
    public function getFormattedIdAttribute(): string
    {
        $prefix = self::prefixForTipe($this->tipe);
        $seq    = static::where('tipe', $this->tipe)
                        ->where('obat_id', '<=', $this->obat_id)
                        ->count();
        return $prefix . '-' . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    // ── Harga beli formatted (Rp) ─────────────────────────────────
    public function getHargaBeliFormattedAttribute(): string
    {
        if (!$this->harga_beli) return '—';
        return 'Rp ' . number_format($this->harga_beli, 0, ',', '.');
    }

    // ── Status ───────────────────────────────────────────────────
    public function getStatusAttribute(): string
    {
        if ($this->tanggal_expired) {
            if (now()->gt($this->tanggal_expired))              return 'expired';
            if (now()->addDays(2)->gte($this->tanggal_expired)) return 'mendekati';
        }

        if ($this->stok <= $this->stok_minimum) return 'menipis';

        return 'aman';
    }

    // ── Status badge ─────────────────────────────────────────────
    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            'expired'   => ['label' => 'Expired',       'class' => 'bg-red-100 text-red-700'],
            'mendekati' => ['label' => 'Mendekati Exp', 'class' => 'bg-yellow-100 text-yellow-700'],
            'menipis'   => ['label' => 'Menipis',       'class' => 'bg-orange-100 text-orange-700'],
            default     => ['label' => 'Aman',          'class' => 'bg-green-100 text-green-700'],
        };
    }
}