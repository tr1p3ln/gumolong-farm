<?php

namespace Tests\Unit;

use App\Models\Domba;
use App\Models\Kandang;
use App\Models\Penimbangan;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DombaModelTest extends TestCase
{
    use DatabaseTransactions;

    // ─── generateEarTag ──────────────────────────────────────────────────────

    public function test_generate_ear_tag_uses_J_prefix_for_jantan(): void
    {
        $tag = Domba::generateEarTag('jantan');

        $this->assertStringStartsWith('J-', $tag);
    }

    public function test_generate_ear_tag_uses_B_prefix_for_betina(): void
    {
        $tag = Domba::generateEarTag('betina');

        $this->assertStringStartsWith('B-', $tag);
    }

    public function test_generate_ear_tag_format_is_correct(): void
    {
        $tag = Domba::generateEarTag('jantan');

        $this->assertMatchesRegularExpression('/^J-\d{3}$/', $tag);
    }

    public function test_generate_ear_tag_increments_sequentially(): void
    {
        $kandang = Kandang::factory()->create();

        $first  = Domba::generateEarTag('jantan');
        Domba::create([
            'ear_tag_id'    => $first,
            'jenis_kelamin' => 'jantan',
            'ras'           => 'Merino',
            'kategori'      => 'pejantan',
            'status'        => 'aktif',
            'kandang_id'    => $kandang->kandang_id,
        ]);

        $second = Domba::generateEarTag('jantan');

        $firstNum  = (int) explode('-', $first)[1];
        $secondNum = (int) explode('-', $second)[1];

        $this->assertEquals($firstNum + 1, $secondNum);
    }

    public function test_generate_ear_tag_independent_sequence_per_gender(): void
    {
        $tagJantan = Domba::generateEarTag('jantan');
        $tagBetina = Domba::generateEarTag('betina');

        $this->assertStringStartsWith('J-', $tagJantan);
        $this->assertStringStartsWith('B-', $tagBetina);
    }

    // ─── getBobotTerakhirAttribute ────────────────────────────────────────────

    public function test_bobot_terakhir_returns_null_when_no_penimbangan(): void
    {
        $domba = Domba::factory()->create();

        $this->assertNull($domba->bobot_terakhir);
    }

    public function test_bobot_terakhir_returns_latest_weight(): void
    {
        $domba = Domba::factory()->create();

        Penimbangan::create([
            'ear_tag_id'      => $domba->ear_tag_id,
            'tanggal_timbang' => now()->subDays(10),
            'berat_kg'        => 20.0,
        ]);
        Penimbangan::create([
            'ear_tag_id'      => $domba->ear_tag_id,
            'tanggal_timbang' => now()->subDays(3),
            'berat_kg'        => 25.5,
        ]);

        $domba->load('penimbangan');

        $this->assertEquals(25.5, $domba->bobot_terakhir);
    }

    // ─── MODEL ATTRIBUTES ────────────────────────────────────────────────────

    public function test_domba_table_is_correct(): void
    {
        $domba = new Domba();

        $this->assertEquals('domba', $domba->getTable());
    }

    public function test_domba_primary_key_is_ear_tag_id(): void
    {
        $domba = new Domba();

        $this->assertEquals('ear_tag_id', $domba->getKeyName());
    }

    public function test_domba_primary_key_is_not_incrementing(): void
    {
        $domba = new Domba();

        $this->assertFalse($domba->getIncrementing());
    }

    // ─── RELATIONSHIPS ────────────────────────────────────────────────────────

    public function test_domba_belongs_to_kandang(): void
    {
        $domba   = Domba::factory()->create();
        $kandang = $domba->kandang;

        $this->assertInstanceOf(Kandang::class, $kandang);
    }

    public function test_domba_has_many_penimbangan(): void
    {
        $domba = Domba::factory()->create();
        Penimbangan::create([
            'ear_tag_id'      => $domba->ear_tag_id,
            'tanggal_timbang' => today(),
            'berat_kg'        => 30.0,
        ]);

        $this->assertCount(1, $domba->penimbangan);
    }

    // ─── SOFT DELETE ──────────────────────────────────────────────────────────

    public function test_domba_uses_soft_deletes(): void
    {
        $domba    = Domba::factory()->create();
        $earTagId = $domba->ear_tag_id;

        $domba->delete();

        $this->assertSoftDeleted('domba', ['ear_tag_id' => $earTagId]);
        $this->assertNull(Domba::find($earTagId));
        $this->assertNotNull(Domba::withTrashed()->find($earTagId));
    }
}
