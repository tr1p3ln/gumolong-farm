<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PakanStok extends Model
{
    protected $table = 'pakan_stok';
    protected $primaryKey = 'pakan_id';

    protected $fillable = [
        'jenis',
        'nama_pakan',
        'jumlah_stok',
        'satuan',
        'stok_minimum',
        'tanggal_update',
    ];

    protected $casts = [
        'tanggal_update' => 'datetime',
        'jumlah_stok'    => 'float',
        'stok_minimum'   => 'float',
    ];

    public $timestamps = true;

    public function pemberianPakan()
    {
        return $this->hasMany(PemberianPakan::class, 'pakan_id', 'pakan_id');
    }

    public function getStatusStokAttribute(): string
    {
        if ($this->jumlah_stok <= 0)                   return 'habis';
        if ($this->jumlah_stok <= $this->stok_minimum) return 'menipis';
        return 'aman';
    }

    public function getPersentaseKapasitasAttribute(): int
    {
        $kapasitasMaks = $this->stok_minimum * 10;
        if ($kapasitasMaks <= 0) return 0;
        return (int) min(100, ($this->jumlah_stok / $kapasitasMaks) * 100);
    }
}