<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Claim;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Integration Test Suite: ReturnFlowIntegrationTest
 * Skenario Utama: Menguji alur pengembalian barang dari awal sampai selesai (end-to-end flow)
 */
class ReturnFlowIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * IT-RTN01: Alur lengkap barang found → klaim → approve → returned
     */
    public function test_full_flow_found_item_claim_approve_return(): void
    {
        $finder = User::factory()->create();
        $owner = User::factory()->create();
        $category = Category::factory()->create();

        // Step 1: Finder melaporkan barang temuan
        $response = $this->actingAs($finder)->postJson('/api/items', [
            'type' => 'found',
            'category_id' => $category->id,
            'title' => 'Dompet ditemukan di kantin',
            'description' => 'Dompet kulit warna coklat berisi KTM',
            'location' => 'Kantin Gedung A',
            'date_time' => '2025-06-15 10:00:00',
        ]);
        $response->assertStatus(201);
        $itemId = $response->json('data.id');

        // Step 2: Owner mengklaim barang
        $response = $this->actingAs($owner)->postJson('/api/claims', [
            'item_id' => $itemId,
            'reason' => 'Ini dompet saya yang hilang kemarin di kantin gedung A',
        ]);
        $response->assertStatus(201);
        $claimId = $response->json('data.id');

        // Step 3: Finder approve klaim
        $response = $this->actingAs($finder)->putJson("/api/claims/{$claimId}/approve");
        $response->assertStatus(200);

        $verificationCode = Claim::find($claimId)->verification_code;

        // Step 4: Finder mark as returned
        $response = $this->actingAs($finder)->putJson("/api/items/{$itemId}/status", [
            'status' => 'returned',
            'verification_code' => $verificationCode,
        ]);
        $response->assertStatus(200);

        // Verifikasi status akhir
        $this->assertDatabaseHas('items', ['id' => $itemId, 'status' => 'returned']);
        $this->assertDatabaseHas('claims', ['id' => $claimId, 'status' => 'approved']);
    }

    /**
     * IT-RTN02: Alur lengkap barang lost → klaim → approve → returned
     */
    public function test_full_flow_lost_item_claim_approve_return(): void
    {
        $loser = User::factory()->create();
        $finder = User::factory()->create();
        $category = Category::factory()->create();

        // Step 1: Loser melaporkan barang hilang
        $response = $this->actingAs($loser)->postJson('/api/items', [
            'type' => 'lost',
            'category_id' => $category->id,
            'title' => 'HP Samsung hilang',
            'description' => 'HP Samsung Galaxy S24 warna hitam',
            'location' => 'Perpustakaan Lt. 2',
            'date_time' => '2025-06-14 14:00:00',
        ]);
        $response->assertStatus(201);
        $itemId = $response->json('data.id');

        // Step 2: Finder mengklaim (menemukan barang)
        $response = $this->actingAs($finder)->postJson('/api/claims', [
            'item_id' => $itemId,
            'reason' => 'Saya menemukan HP ini di perpustakaan lantai 2 kemarin',
        ]);
        $response->assertStatus(201);
        $claimId = $response->json('data.id');

        // Step 3: Loser approve klaim
        $response = $this->actingAs($loser)->putJson("/api/claims/{$claimId}/approve");
        $response->assertStatus(200);

        $verificationCode = Claim::find($claimId)->verification_code;

        // Step 4: Finder (approved claimer) mark as returned
        $response = $this->actingAs($finder)->putJson("/api/items/{$itemId}/status", [
            'status' => 'returned',
            'verification_code' => $verificationCode,
        ]);
        $response->assertStatus(200);

        // Verifikasi
        $this->assertDatabaseHas('items', ['id' => $itemId, 'status' => 'returned']);
    }

    /**
     * IT-RTN03: User yang bukan pemilik/claimer tidak bisa update status
     */
    public function test_unauthorized_user_cannot_update_status(): void
    {
        $owner = User::factory()->create();
        $randomUser = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $owner->id,
            'type' => 'found',
            'status' => 'claimed',
        ]);

        $response = $this->actingAs($randomUser)->putJson("/api/items/{$item->id}/status", [
            'status' => 'returned',
        ]);

        $response->assertStatus(403);
    }

    /**
     * IT-RTN04: Alur reject klaim lalu klaim ulang oleh user lain
     */
    public function test_reject_then_new_claim_by_different_user(): void
    {
        $owner = User::factory()->create();
        $claimer1 = User::factory()->create();
        $claimer2 = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $owner->id,
            'type' => 'found',
            'status' => 'active',
        ]);

        // Claimer 1 klaim
        $claim1 = Claim::factory()->create([
            'item_id' => $item->id,
            'claimer_id' => $claimer1->id,
            'status' => 'pending',
        ]);

        // Owner reject
        $this->actingAs($owner)->putJson("/api/claims/{$claim1->id}/reject", [
            'reason' => 'Deskripsi tidak sesuai dengan barang',
        ])->assertStatus(200);

        // Claimer 2 bisa klaim
        $response = $this->actingAs($claimer2)->postJson('/api/claims', [
            'item_id' => $item->id,
            'reason' => 'Ini barang saya, ciri-cirinya sesuai dengan yang hilang',
        ]);
        $response->assertStatus(201);

        // Item masih active (belum di-approve)
        $this->assertEquals('active', $item->refresh()->status);
    }

    /**
     * IT-RTN05: Pemilik barang found bisa langsung mark returned tanpa klaim
     */
    public function test_found_item_owner_can_mark_returned(): void
    {
        $finder = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $finder->id,
            'type' => 'found',
            'status' => 'active',
        ]);

        $response = $this->actingAs($finder)->putJson("/api/items/{$item->id}/status", [
            'status' => 'returned',
        ]);

        $response->assertStatus(200);
        $this->assertEquals('returned', $item->refresh()->status);
    }

    /**
     * IT-RTN06: Pemilik barang lost TIDAK bisa mark returned sendiri
     */
    public function test_lost_item_owner_cannot_mark_returned(): void
    {
        $loser = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $loser->id,
            'type' => 'lost',
            'status' => 'active',
        ]);

        $response = $this->actingAs($loser)->putJson("/api/items/{$item->id}/status", [
            'status' => 'returned',
        ]);

        $response->assertStatus(403);
    }

    /**
     * IT-RTN07: Leaderboard menampilkan user dengan kontribusi
     */
    public function test_leaderboard_shows_contributing_users(): void
    {
        $finder = User::factory()->create();
        // Buat item found yang sudah returned
        Item::factory()->create([
            'user_id' => $finder->id,
            'type' => 'found',
            'status' => 'returned',
        ]);

        $viewer = User::factory()->create();
        $response = $this->actingAs($viewer)->getJson('/api/leaderboard');

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $leaderboard = $response->json('data.leaderboard');
        $this->assertNotEmpty($leaderboard);
        $this->assertEquals($finder->id, $leaderboard[0]['user']['id']);
    }
}
