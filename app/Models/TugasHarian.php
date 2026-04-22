<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TugasHarian extends Model
{
    protected $table      = 'tugas_harian';
    protected $primaryKey = 'id';

    protected $fillable = [
        'kandang_id',
        'user_id',
        'judul',
        'deskripsi',
        'tanggal',
        'prioritas',
        'status',
        'waktu_mulai',
        'waktu_selesai',
        'catatan_penyelesaian',
    ];

    protected $casts = [
        'tanggal'       => 'date',
        'waktu_mulai'   => 'datetime:H:i',
        'waktu_selesai' => 'datetime:H:i',
    ];

    // ─── RELATIONS ───────────────────────────────────────────────

    public function kandang()
    {
        return $this->belongsTo(Kandang::class, 'kandang_id', 'kandang_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // ─── SCOPES ──────────────────────────────────────────────────

    public function scopeHariIni($query)
    {
        return $query->whereDate('tanggal', today());
    }

    public function scopeTanggal($query, $tanggal)
    {
        return $query->whereDate('tanggal', $tanggal);
    }

    public function scopeKandang($query, $kandangId)
    {
        return $query->where('kandang_id', $kandangId);
    }

    public function scopeBelumSelesai($query)
    {
        return $query->whereIn('status', ['belum', 'dalam_proses']);
    }

    // ─── ACCESSORS ───────────────────────────────────────────────

    public function getPrioritasColorAttribute(): string
    {
        return match ($this->prioritas) {
            'tinggi' => 'red',
            'sedang' => 'amber',
            'rendah' => 'green',
            default  => 'gray',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'belum'        => 'Belum Dikerjakan',
            'dalam_proses' => 'Dalam Proses',
            'selesai'      => 'Selesai',
            'dilewati'     => 'Dilewati',
            default        => '-',
        };
    }

    public function getDurasiAttribute(): ?string
    {
        if (!$this->waktu_mulai || !$this->waktu_selesai) return null;
        $mulai   = \Carbon\Carbon::parse($this->waktu_mulai);
        $selesai = \Carbon\Carbon::parse($this->waktu_selesai);
        $menit   = $mulai->diffInMinutes($selesai);
        return $menit >= 60
            ? floor($menit / 60) . 'j ' . ($menit % 60) . 'm'
            : $menit . ' menit';
    }
}
