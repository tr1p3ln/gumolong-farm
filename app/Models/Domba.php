<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Domba extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'domba';

    protected $primaryKey = 'ear_tag_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'ear_tag_id',
        'nama',
        'jenis_kelamin',
        'ras',
        'tanggal_lahir',
        'kategori',
        'status',
        'asal',
        'catatan',
        'kandang_id',
        'induk_id',
        'ayah_id',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'kategori'      => 'string',
        'status'        => 'string',
        'asal'          => 'string',
        'jenis_kelamin' => 'string',
        'deleted_at'    => 'datetime',
    ];

    protected $appends = ['bobot_terakhir'];

    public function kandang(): BelongsTo
    {
        return $this->belongsTo(Kandang::class, 'kandang_id', 'kandang_id');
    }

    public function induk(): BelongsTo
    {
        return $this->belongsTo(Domba::class, 'induk_id', 'ear_tag_id');
    }

    public function ayah(): BelongsTo
    {
        return $this->belongsTo(Domba::class, 'ayah_id', 'ear_tag_id');
    }

    public function anak(): HasMany
    {
        return $this->hasMany(Domba::class, 'induk_id', 'ear_tag_id');
    }

    public function penimbangan(): HasMany
    {
        return $this->hasMany(Penimbangan::class, 'ear_tag_id', 'ear_tag_id');
    }

    public function getBobotTerakhirAttribute(): ?float
    {
        $latest = $this->relationLoaded('penimbangan')
            ? $this->penimbangan->sortByDesc('tanggal_timbang')->first()
            : $this->penimbangan()->orderByDesc('tanggal_timbang')->first();

        return $latest ? (float) $latest->berat_kg : null;
    }

    public static function generateEarTag(string $jenisKelamin): string
    {
        $prefix = $jenisKelamin === 'jantan' ? 'J' : 'B';

        return DB::transaction(function () use ($prefix) {
            // PostgreSQL advisory lock per-prefix (B atau J).
            // crc32() -> integer 32-bit yang dibutuhkan pg_advisory_xact_lock().
            // Lock otomatis release saat transaction commit.
            DB::statement(
                'SELECT pg_advisory_xact_lock(?)',
                [crc32('gumolong_eartag_' . $prefix)]
            );

            $maxNumber = DB::table('domba')
                ->where('ear_tag_id', 'like', $prefix . '-%')
                ->max(DB::raw("CAST(SPLIT_PART(ear_tag_id, '-', 2) AS INTEGER)"));

            $nextNumber = ((int) ($maxNumber ?? 0)) + 1;

            return $prefix . '-' . str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);
        });
    }
}
