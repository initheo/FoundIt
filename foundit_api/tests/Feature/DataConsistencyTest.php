<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Claim;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Integration Test Suite: DataConsistencyTest
 * Skenario Utama: Verifikasi konsistensi data dalam sistem
 *
 * Memastikan bahwa total jumlah barang, status, dan klaim
 * selalu konsisten sebelum dan sesudah operasi kritikal.
 */
class DataConsistencyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * VKD-01: Approve klaim tidak mengubah total jumlah barang dalam sistem
     *
     * Total item = 5 tetap sebelum dan sesudah approve klaim.
     * Hanya status yang berubah, jumlah record tidak bertambah/berkurang.
     */
    public function test_approve_klaim_tidak_mengubah_total_jumlah_barang(): void
    {
        $owner = User::factory()->create();
        $claimer = User::factory()->create();
        $category = Category::factory()->create();

        // Buat 5 item
        $items = Item::factory()->count(5)->create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'status' => 'active',
        ]);

        $claim = Claim::factory()->create([
            'item_id' => $items[0]->id,
            'claimer_id' => $claimer->id,
            'status' => 'pending',
        ]);

        // Total sebelum approve
        $totalSebelum = Item::count();
        $this->assertEquals(5, $totalSebelum);

        // Approve klaim
        $this->actingAs($owner)->putJson("/api/claims/{$claim->id}/approve");

        // Total sesudah approve — tetap 5, tidak ada item hilang/bertambah
        $totalSesudah = Item::count();
        $this->assertEquals(5, $totalSesudah);
        $this->assertEquals($totalSebelum, $totalSesudah);

        // Verifikasi hanya 1 item yang berubah status
        $this->assertEquals(4, Item::where('status', 'active')->count());
        $this->assertEquals(1, Item::where('status', 'claimed')->count());
    }

    /**
     * VKD-02: Alur klaim berantai tiga user tetap konsisten
     *
     * User A punya 3 item, User B dan C masing-masing klaim 1 item berbeda.
     * Setelah semua di-approve, total item tetap 3 dan status konsisten.
     */
    public function test_klaim_berantai_tiga_user_tetap_konsisten(): void
    {
        $owner = User::factory()->create();
        $claimerB = User::factory()->create();
        $claimerC = User::factory()->create();
        $category = Category::factory()->create();

        // Owner punya 3 item active
        $item1 = Item::factory()->create(['user_id' => $owner->id, 'category_id' => $category->id, 'status' => 'active']);
        $item2 = Item::factory()->create(['user_id' => $owner->id, 'category_id' => $category->id, 'status' => 'active']);
        $item3 = Item::factory()->create(['user_id' => $owner->id, 'category_id' => $category->id, 'status' => 'active']);

        // Total awal
        $totalItemAwal = Item::count();
        $this->assertEquals(3, $totalItemAwal);

        // User B klaim item 1
        $claim1 = Claim::factory()->create(['item_id' => $item1->id, 'claimer_id' => $claimerB->id, 'status' => 'pending']);
        // User C klaim item 2
        $claim2 = Claim::factory()->create(['item_id' => $item2->id, 'claimer_id' => $claimerC->id, 'status' => 'pending']);

        // Owner approve kedua klaim
        $this->actingAs($owner)->putJson("/api/claims/{$claim1->id}/approve");
        $this->actingAs($owner)->putJson("/api/claims/{$claim2->id}/approve");

        // Verifikasi konsistensi: total item tetap 3
        $totalItemAkhir = Item::count();
        $this->assertEquals(3, $totalItemAkhir);
        $this->assertEquals($totalItemAwal, $totalItemAkhir);

        // Verifikasi distribusi status konsisten (2 claimed + 1 active = 3)
        $activeCount = Item::where('status', 'active')->count();
        $claimedCount = Item::where('status', 'claimed')->count();
        $this->assertEquals(1, $activeCount);
        $this->assertEquals(2, $claimedCount);
        $this->assertEquals($totalItemAkhir, $activeCount + $claimedCount);
    }

    /**
     * VKD-03: Hapus item dan klaim terkait tetap konsisten
     *
     * Setelah item dihapus, klaim terkait tidak menjadi orphan
     * dan total data tetap akurat.
     */
    public function test_hapus_item_dan_total_klaim_konsisten(): void
    {
        $owner = User::factory()->create();
        $claimer = User::factory()->create();
        $category = Category::factory()->create();

        // Buat 3 item, masing-masing punya 1 klaim
        $item1 = Item::factory()->create(['user_id' => $owner->id, 'category_id' => $category->id]);
        $item2 = Item::factory()->create(['user_id' => $owner->id, 'category_id' => $category->id]);
        $item3 = Item::factory()->create(['user_id' => $owner->id, 'category_id' => $category->id]);

        Claim::factory()->create(['item_id' => $item1->id, 'claimer_id' => $claimer->id]);
        Claim::factory()->create(['item_id' => $item2->id, 'claimer_id' => $claimer->id]);
        Claim::factory()->create(['item_id' => $item3->id, 'claimer_id' => $claimer->id]);

        // Total awal: 3 item, 3 klaim
        $this->assertEquals(3, Item::count());
        $this->assertEquals(3, Claim::count());

        // Hapus item 1
        $this->actingAs($owner)->deleteJson("/api/items/{$item1->id}");

        // Total sesudah: 2 item (berkurang 1)
        $this->assertEquals(2, Item::count());

        // Klaim untuk item yang tersisa tetap ada
        $this->assertEquals(1, Claim::where('item_id', $item2->id)->count());
        $this->assertEquals(1, Claim::where('item_id', $item3->id)->count());
    }

    /**
     * VKD-04: Reject klaim tidak mengubah status item (tetap active)
     *
     * Setelah semua klaim di-reject, item tetap active dan
     * total status dalam sistem konsisten.
     */
    public function test_reject_semua_klaim_item_tetap_active(): void
    {
        $owner = User::factory()->create();
        $claimer1 = User::factory()->create();
        $claimer2 = User::factory()->create();
        $category = Category::factory()->create();

        $item = Item::factory()->create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'status' => 'active',
        ]);

        $claim1 = Claim::factory()->create(['item_id' => $item->id, 'claimer_id' => $claimer1->id, 'status' => 'pending']);
        $claim2 = Claim::factory()->create(['item_id' => $item->id, 'claimer_id' => $claimer2->id, 'status' => 'pending']);

        // Status awal
        $this->assertEquals('active', $item->status);
        $this->assertEquals(2, Claim::where('status', 'pending')->count());

        // Reject kedua klaim
        $this->actingAs($owner)->putJson("/api/claims/{$claim1->id}/reject", ['reason' => 'Deskripsi tidak sesuai dengan barang']);
        $this->actingAs($owner)->putJson("/api/claims/{$claim2->id}/reject", ['reason' => 'Bukti kepemilikan tidak valid']);

        // Item tetap active (tidak berubah)
        $this->assertEquals('active', $item->refresh()->status);

        // Semua klaim menjadi rejected
        $this->assertEquals(0, Claim::where('status', 'pending')->count());
        $this->assertEquals(2, Claim::where('status', 'rejected')->count());

        // Total klaim tetap 2 (tidak ada yang hilang)
        $this->assertEquals(2, Claim::count());
    }

    /**
     * VKD-05: Approve satu klaim otomatis reject lainnya — total tetap konsisten
     *
     * Dari 3 klaim pending, approve 1 → 1 approved + 2 rejected = 3 total.
     * Tidak ada klaim yang hilang atau bertambah.
     */
    public function test_approve_satu_reject_lainnya_total_konsisten(): void
    {
        $owner = User::factory()->create();
        $claimer1 = User::factory()->create();
        $claimer2 = User::factory()->create();
        $claimer3 = User::factory()->create();
        $category = Category::factory()->create();

        $item = Item::factory()->create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'status' => 'active',
        ]);

        $claim1 = Claim::factory()->create(['item_id' => $item->id, 'claimer_id' => $claimer1->id, 'status' => 'pending']);
        $claim2 = Claim::factory()->create(['item_id' => $item->id, 'claimer_id' => $claimer2->id, 'status' => 'pending']);
        $claim3 = Claim::factory()->create(['item_id' => $item->id, 'claimer_id' => $claimer3->id, 'status' => 'pending']);

        // Total klaim awal: 3 pending
        $totalKlaimAwal = Claim::count();
        $this->assertEquals(3, $totalKlaimAwal);
        $this->assertEquals(3, Claim::where('status', 'pending')->count());

        // Approve klaim 1
        $this->actingAs($owner)->putJson("/api/claims/{$claim1->id}/approve");

        // Total klaim akhir tetap 3 (tidak ada yang hilang)
        $totalKlaimAkhir = Claim::count();
        $this->assertEquals(3, $totalKlaimAkhir);
        $this->assertEquals($totalKlaimAwal, $totalKlaimAkhir);

        // Distribusi status: 1 approved + 2 rejected = 3
        $approvedCount = Claim::where('status', 'approved')->count();
        $rejectedCount = Claim::where('status', 'rejected')->count();
        $pendingCount = Claim::where('status', 'pending')->count();

        $this->assertEquals(1, $approvedCount);
        $this->assertEquals(2, $rejectedCount);
        $this->assertEquals(0, $pendingCount);
        $this->assertEquals($totalKlaimAkhir, $approvedCount + $rejectedCount + $pendingCount);
    }

    /**
     * VKD-06: Full flow returned — statistik tetap konsisten
     *
     * Setelah item di-return, statistik (active + returned) = total item.
     */
    public function test_return_flow_statistik_tetap_konsisten(): void
    {
        $owner = User::factory()->create();
        $claimer = User::factory()->create();
        $category = Category::factory()->create();

        // Buat 5 item found active
        Item::factory()->count(5)->create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'type' => 'found',
            'status' => 'active',
        ]);

        $targetItem = Item::first();

        // Klaim dan approve
        $claim = Claim::factory()->create([
            'item_id' => $targetItem->id,
            'claimer_id' => $claimer->id,
            'status' => 'pending',
        ]);
        $this->actingAs($owner)->putJson("/api/claims/{$claim->id}/approve");

        // Mark as returned
        $this->actingAs($owner)->putJson("/api/items/{$targetItem->id}/status", [
            'status' => 'returned',
        ]);

        // Verifikasi konsistensi statistik
        $totalItem = Item::count();
        $activeCount = Item::where('status', 'active')->count();
        $claimedCount = Item::where('status', 'claimed')->count();
        $returnedCount = Item::where('status', 'returned')->count();

        $this->assertEquals(5, $totalItem);
        $this->assertEquals(4, $activeCount);
        $this->assertEquals(0, $claimedCount);
        $this->assertEquals(1, $returnedCount);

        // Total semua status = total item (tidak ada data hilang)
        $this->assertEquals($totalItem, $activeCount + $claimedCount + $returnedCount);
    }
}
