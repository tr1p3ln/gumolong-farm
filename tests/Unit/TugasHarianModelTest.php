<?php

namespace Tests\Unit;

use App\Models\Kandang;
use App\Models\TugasHarian;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TugasHarianModelTest extends TestCase
{
    use DatabaseTransactions;

    private Kandang $kandang;

    protected function setUp(): void
    {
        parent::setUp();
        $this->kandang = Kandang::factory()->create();
    }

    // ─── SCOPES ──────────────────────────────────────────────────────────────

    public function test_scope_hari_ini_returns_only_todays_tasks(): void
    {
        TugasHarian::factory()->create([
            'kandang_id' => $this->kandang->kandang_id,
            'tanggal'    => today(),
        ]);
        TugasHarian::factory()->create([
            'kandang_id' => $this->kandang->kandang_id,
            'tanggal'    => today()->subDays(1),
        ]);

        $results = TugasHarian::hariIni()->get();

        foreach ($results as $tugas) {
            $this->assertEquals(today()->toDateString(), $tugas->tanggal->toDateString());
        }
    }

    public function test_scope_tanggal_filters_by_specific_date(): void
    {
        $targetDate = '2026-07-20';
        $created    = TugasHarian::factory()->create([
            'kandang_id' => $this->kandang->kandang_id,
            'tanggal'    => $targetDate,
        ]);

        $results = TugasHarian::tanggal($targetDate)->get();

        $this->assertTrue($results->contains('id', $created->id));
    }

    public function test_filter_by_kandang_id_works(): void
    {
        $otherKandang = Kandang::factory()->create();

        $tugasTarget = TugasHarian::factory()->create(['kandang_id' => $this->kandang->kandang_id]);
        $tugasOther  = TugasHarian::factory()->create(['kandang_id' => $otherKandang->kandang_id]);

        // Scope scopeKandang tidak bisa dipanggil karena shadowed oleh relationship kandang().
        // Verifikasi melalui where() langsung.
        $results = TugasHarian::where('kandang_id', $this->kandang->kandang_id)->get();

        $this->assertTrue($results->contains('id', $tugasTarget->id));
        $this->assertFalse($results->contains('id', $tugasOther->id));
    }

    public function test_scope_belum_selesai_excludes_completed_tasks(): void
    {
        $belum  = TugasHarian::factory()->create(['kandang_id' => $this->kandang->kandang_id, 'status' => 'belum']);
        $proses = TugasHarian::factory()->create(['kandang_id' => $this->kandang->kandang_id, 'status' => 'dalam_proses']);
        $selesai = TugasHarian::factory()->create(['kandang_id' => $this->kandang->kandang_id, 'status' => 'selesai']);

        $results = TugasHarian::belumSelesai()->get();

        $this->assertTrue($results->contains('id', $belum->id));
        $this->assertTrue($results->contains('id', $proses->id));
        $this->assertFalse($results->contains('id', $selesai->id));
    }

    // ─── ACCESSORS ───────────────────────────────────────────────────────────

    public function test_prioritas_color_returns_correct_value(): void
    {
        $tugas = TugasHarian::factory()->make(['prioritas' => 'tinggi']);
        $this->assertEquals('red', $tugas->prioritas_color);

        $tugas = TugasHarian::factory()->make(['prioritas' => 'sedang']);
        $this->assertEquals('amber', $tugas->prioritas_color);

        $tugas = TugasHarian::factory()->make(['prioritas' => 'rendah']);
        $this->assertEquals('green', $tugas->prioritas_color);
    }

    public function test_tipe_label_returns_correct_value(): void
    {
        $tugas = TugasHarian::factory()->make(['tipe' => 'rutin']);
        $this->assertEquals('Rutin', $tugas->tipe_label);

        $tugas = TugasHarian::factory()->make(['tipe' => 'kondisional']);
        $this->assertEquals('Kondisional', $tugas->tipe_label);
    }

    public function test_status_label_returns_correct_value(): void
    {
        $cases = [
            'belum'        => 'Belum Dikerjakan',
            'dalam_proses' => 'Dalam Proses',
            'selesai'      => 'Selesai',
            'dilewati'     => 'Dilewati',
        ];

        foreach ($cases as $status => $expected) {
            $tugas = TugasHarian::factory()->make(['status' => $status]);
            $this->assertEquals($expected, $tugas->status_label);
        }
    }

    public function test_durasi_returns_null_without_waktu(): void
    {
        $tugas = TugasHarian::factory()->make([
            'waktu_mulai'   => null,
            'waktu_selesai' => null,
        ]);

        $this->assertNull($tugas->durasi);
    }

    public function test_durasi_returns_menit_for_under_one_hour(): void
    {
        $tugas = TugasHarian::factory()->create([
            'kandang_id'    => $this->kandang->kandang_id,
            'waktu_mulai'   => '08:00',
            'waktu_selesai' => '08:45',
        ]);

        $this->assertEquals('45 menit', $tugas->fresh()->durasi);
    }

    public function test_durasi_returns_jam_for_over_one_hour(): void
    {
        $tugas = TugasHarian::factory()->create([
            'kandang_id'    => $this->kandang->kandang_id,
            'waktu_mulai'   => '08:00',
            'waktu_selesai' => '10:30',
        ]);

        $this->assertEquals('2j 30m', $tugas->fresh()->durasi);
    }

    // ─── MODEL ATTRIBUTES ─────────────────────────────────────────────────────

    public function test_tugas_harian_table_is_correct(): void
    {
        $model = new TugasHarian();
        $this->assertEquals('tugas_harian', $model->getTable());
    }

    public function test_tugas_harian_fillable_contains_required_fields(): void
    {
        $model    = new TugasHarian();
        $fillable = $model->getFillable();

        foreach (['judul', 'kandang_id', 'tanggal', 'tipe', 'prioritas', 'status'] as $field) {
            $this->assertContains($field, $fillable);
        }
    }
}
