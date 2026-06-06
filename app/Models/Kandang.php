<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kandang extends Model
{
    use HasFactory;

    protected $table = 'kandang';

    protected $primaryKey = 'kandang_id';

    protected $fillable = [
        'nama_kandang',
        'tipe',
        'kapasitas',
    ];

    protected $casts = [
        'kapasitas' => 'integer',
    ];

    protected $appends = ['sisa_slot'];

    public function domba(): HasMany
    {
        return $this->hasMany(Domba::class, 'kandang_id', 'kandang_id');
    }

    public function getSisaSlotAttribute(): int
    {
        $terpakai = $this->domba()->where('status', 'aktif')->count();

        return max(0, (int) $this->kapasitas - $terpakai);
    }
}
