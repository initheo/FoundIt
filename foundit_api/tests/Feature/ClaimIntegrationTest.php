<?php

namespace Tests\Feature;

use App\Models\Claim;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Integration Test Suite: ClaimIntegrationTest
 * Skenario Utama: Menguji alur klaim barang (buat, approve, reject) secara end-to-end
 */
class ClaimIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private User $claimer;
    private Item $item;

    protected function setUp(): void
    {
        parent::setUp();
        $this->owner = User::factory()->create();
        $this->claimer = User::factory()->create();
        $this->item = Item::factory()->create([
            'user_id' => $this->owner->id,
            'type' => 'found',
            'status' => 'active',
        ]);
    }

    /**
     * IT-CLM01: Membuat klaim berhasil dengan alasan valid
     */
    public function test_create_claim_success(): void
    {
        $response = $this->actingAs($this->claimer)->postJson('/api/claims', [
            'item_id' => $this->item->id,
            'reason' => 'Ini barang saya yang hilang minggu lalu di kantin gedung A',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('claims', [
            'item_id' => $this->item->id,
            'claimer_id' => $this->claimer->id,
            'status' => 'pending',
        ]);
    }

    /**
     * IT-CLM02: Tidak bisa klaim barang sendiri
     */
    public function test_cannot_claim_own_item(): void
    {
        $response = $this->actingAs($this->owner)->postJson('/api/claims', [
            'item_id' => $this->item->id,
            'reason' => 'Ini barang saya sendiri yang saya posting',
        ]);

        $response->assertStatus(422);
    }

    /**
     * IT-CLM03: Tidak bisa klaim dua kali pada barang yang sama
     */
    public function test_cannot_claim_same_item_twice(): void
    {
        Claim::factory()->create([
            'item_id' => $this->item->id,
            'claimer_id' => $this->claimer->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->claimer)->postJson('/api/claims', [
            'item_id' => $this->item->id,
            'reason' => 'Saya coba klaim lagi untuk barang yang sama',
        ]);

        $response->assertStatus(422);
    }

    /**
     * IT-CLM04: Approve klaim oleh pemilik barang berhasil
     */
    public function test_approve_claim_by_owner_success(): void
    {
        $claim = Claim::factory()->create([
            'item_id' => $this->item->id,
            'claimer_id' => $this->claimer->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->owner)->putJson("/api/claims/{$claim->id}/approve");

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'approved');

        // Item status berubah menjadi claimed
        $this->assertEquals('claimed', $this->item->refresh()->status);
    }

    /**
     * IT-CLM05: Approve klaim otomatis reject klaim lainnya
     */
    public function test_approve_claim_rejects_other_pending_claims(): void
    {
        $claimer2 = User::factory()->create();

        $claim1 = Claim::factory()->create([
            'item_id' => $this->item->id,
            'claimer_id' => $this->claimer->id,
            'status' => 'pending',
        ]);
        $claim2 = Claim::factory()->create([
            'item_id' => $this->item->id,
            'claimer_id' => $claimer2->id,
            'status' => 'pending',
        ]);

        $this->actingAs($this->owner)->putJson("/api/claims/{$claim1->id}/approve");

        $this->assertEquals('approved', $claim1->refresh()->status);
        $this->assertEquals('rejected', $claim2->refresh()->status);
    }

    /**
     * IT-CLM06: Reject klaim oleh pemilik barang berhasil
     */
    public function test_reject_claim_by_owner_success(): void
    {
        $claim = Claim::factory()->create([
            'item_id' => $this->item->id,
            'claimer_id' => $this->claimer->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->owner)->putJson("/api/claims/{$claim->id}/reject", [
            'reason' => 'Deskripsi barang tidak sesuai',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'rejected');

        $this->assertEquals('rejected', $claim->refresh()->status);
    }

    /**
     * IT-CLM07: Approve/reject klaim oleh bukan pemilik ditolak
     */
    public function test_approve_claim_by_non_owner_rejected(): void
    {
        $claim = Claim::factory()->create([
            'item_id' => $this->item->id,
            'claimer_id' => $this->claimer->id,
            'status' => 'pending',
        ]);

        $randomUser = User::factory()->create();
        $response = $this->actingAs($randomUser)->putJson("/api/claims/{$claim->id}/approve");

        $response->assertStatus(403);
    }

    /**
     * IT-CLM08: Melihat daftar klaim saya (My Claims)
     */
    public function test_my_claims_returns_own_claims(): void
    {
        Claim::factory()->count(3)->create(['claimer_id' => $this->claimer->id]);
        Claim::factory()->count(2)->create(); // klaim user lain

        $response = $this->actingAs($this->claimer)->getJson('/api/claims/my');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    /**
     * IT-CLM09: Tidak bisa klaim barang yang sudah tidak aktif
     */
    public function test_cannot_claim_inactive_item(): void
    {
        $this->item->update(['status' => 'returned']);

        $response = $this->actingAs($this->claimer)->postJson('/api/claims', [
            'item_id' => $this->item->id,
            'reason' => 'Saya coba klaim barang yang sudah returned',
        ]);

        $response->assertStatus(422);
    }

    /**
     * Klaim gagal jika alasan kurang dari 20 karakter
     */
    public function test_claim_fails_with_short_reason(): void
    {
        $response = $this->actingAs($this->claimer)->postJson('/api/claims', [
            'item_id' => $this->item->id,
            'reason' => 'Terlalu pendek',
        ]);

        $response->assertStatus(422);
    }

    public function test_index_item_claims_success_for_owner()
    {
        Claim::factory()->create([
            'item_id' => $this->item->id,
            'claimer_id' => $this->claimer->id,
        ]);

        $response = $this->actingAs($this->owner)->getJson("/api/items/{$this->item->id}/claims");
        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function test_index_item_claims_returns_403_for_non_owner()
    {
        $randomUser = User::factory()->create();
        $response = $this->actingAs($randomUser)->getJson("/api/items/{$this->item->id}/claims");
        $response->assertStatus(403);
    }

    public function test_index_item_claims_returns_404_for_nonexistent_item()
    {
        $response = $this->actingAs($this->owner)->getJson("/api/items/99999/claims");
        $response->assertStatus(404);
    }

    public function test_cannot_claim_if_already_has_approved_claim()
    {
        Claim::factory()->create([
            'item_id' => $this->item->id,
            'claimer_id' => $this->claimer->id,
            'status' => 'approved',
        ]);

        $response = $this->actingAs($this->claimer)->postJson('/api/claims', [
            'item_id' => $this->item->id,
            'reason' => 'Alasan baru yang cukup panjang untuk klaim barang saya',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Klaim Anda sudah disetujui sebelumnya');
    }

    public function test_cannot_claim_if_already_has_pending_claim()
    {
        Claim::factory()->create([
            'item_id' => $this->item->id,
            'claimer_id' => $this->claimer->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->claimer)->postJson('/api/claims', [
            'item_id' => $this->item->id,
            'reason' => 'Alasan baru yang cukup panjang untuk klaim barang saya',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Anda sudah memiliki klaim yang sedang menunggu review');
    }

    public function test_cannot_approve_non_pending_claim()
    {
        $claim = Claim::factory()->create([
            'item_id' => $this->item->id,
            'claimer_id' => $this->claimer->id,
            'status' => 'approved',
        ]);

        $response = $this->actingAs($this->owner)->putJson("/api/claims/{$claim->id}/approve");
        $response->assertStatus(422)
            ->assertJsonPath('message', 'Klaim sudah diproses sebelumnya');
    }

    public function test_cannot_reject_non_pending_claim()
    {
        $claim = Claim::factory()->create([
            'item_id' => $this->item->id,
            'claimer_id' => $this->claimer->id,
            'status' => 'rejected',
        ]);

        $response = $this->actingAs($this->owner)->putJson("/api/claims/{$claim->id}/reject", [
            'reason' => 'Alasan baru reject',
        ]);
        $response->assertStatus(422)
            ->assertJsonPath('message', 'Klaim sudah diproses sebelumnya');
    }

    public function test_approve_claim_returns_404_for_nonexistent_claim()
    {
        $response = $this->actingAs($this->owner)->putJson("/api/claims/99999/approve");
        $response->assertStatus(404);
    }

    public function test_reject_claim_returns_404_for_nonexistent_claim()
    {
        $response = $this->actingAs($this->owner)->putJson("/api/claims/99999/reject", [
            'reason' => 'Alasan reject',
        ]);
        $response->assertStatus(404);
    }
}
