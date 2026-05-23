<?php

namespace Tests\Feature;

use App\Models\Claim;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ItemIntegrationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * IT-01: Create Claim (User B klaim Item A).
     */
    public function test_it_01_create_claim(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $userA->id, 'type' => 'lost']);

        $response = $this->actingAs($userB)->postJson('/api/claims', [
            'item_id' => $item->id,
            'reason' => 'Saya menemukan barang ini di kantin.'
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('claims', ['item_id' => $item->id, 'claimer_id' => $userB->id]);
    }

    /**
     * IT-02: Approve Claim (User A approve Klaim B).
     */
    public function test_it_02_approve_claim(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $userA->id, 'status' => 'active']);
        $claim = Claim::factory()->create(['item_id' => $item->id, 'claimer_id' => $userB->id, 'status' => 'pending']);

        $response = $this->actingAs($userA)->putJson("/api/claims/{$claim->id}/approve");

        $response->assertStatus(200);
        $this->assertEquals('claimed', $item->refresh()->status);
    }

    /**
     * IT-03: Return Item (User B mark returned).
     */
    public function test_it_03_return_item(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $userA->id, 'type' => 'lost', 'status' => 'claimed']);
        
        // Buat klaim yang sudah approved agar bisa update status
        Claim::factory()->create([
            'item_id' => $item->id, 
            'claimer_id' => $userB->id, 
            'status' => 'approved'
        ]);

        $response = $this->actingAs($userB)->putJson("/api/items/{$item->id}/status", [
            'status' => 'returned'
        ]);

        $response->assertStatus(200);
        $this->assertEquals('returned', $item->refresh()->status);
    }

    /**
     * IT-04: Access Control (User C edit/delete Item A).
     */
    public function test_it_04_access_control(): void
    {
        $userA = User::factory()->create();
        $userC = User::factory()->create();
        $itemA = Item::factory()->create(['user_id' => $userA->id]);

        $response = $this->actingAs($userC)->deleteJson("/api/items/{$itemA->id}");

        $response->assertStatus(404); // Controller returns 404 if not found for that user
    }

    /**
     * IT-05: Category Filter (Filter by Category).
     */
    public function test_it_05_category_filter(): void
    {
        $user = User::factory()->create();
        $cat1 = \App\Models\Category::factory()->create(['name' => 'Elektronik']);
        $cat2 = \App\Models\Category::factory()->create(['name' => 'Dokumen']);
        
        Item::factory()->create(['category_id' => $cat1->id, 'title' => 'HP Samsung']);
        Item::factory()->create(['category_id' => $cat2->id, 'title' => 'KTM']);

        $response = $this->actingAs($user)->getJson("/api/items?category_id={$cat1->id}");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'HP Samsung');
    }

    /**
     * IT-06: Extra - Photo Management.
     */
    public function test_it_06_photo_management(): void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->postJson("/api/items/{$item->id}/photos", [
            'photo' => \Illuminate\Http\UploadedFile::fake()->image('test.jpg')
        ]);

        $response->assertStatus(200);
        $photoId = $response->json('data.id');

        $this->actingAs($user)->deleteJson("/api/items/{$item->id}/photos/{$photoId}")
            ->assertStatus(200);
    }
}
