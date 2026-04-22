<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penimbangan extends Model
{
    use HasFactory;

    protected $table = 'penimbangan';

    protected $primaryKey = 'timbangan_id';

    protected $fillable = [
        'ear_tag_id',
        'tanggal_timbang',
        'berat_kg',
        'adg',
        'catatan',
    ];

    protected $casts = [
        'tanggal_timbang' => 'date',
        'berat_kg'        => 'decimal:2',
        'adg'             => 'decimal:3',
    ];

    public function domba(): BelongsTo
    {
        return $this->belongsTo(Domba::class, 'ear_tag_id', 'ear_tag_id');
    }
}
