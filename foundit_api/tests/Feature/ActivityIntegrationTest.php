<?php

namespace Tests\Feature;

use App\Models\Claim;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_activity_index_requires_authentication()
    {
        $response = $this->getJson('/api/activities');
        $response->assertStatus(401);
    }

    public function test_activity_index_returns_user_activities()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        // 1. Create reported item by user
        $item = Item::factory()->create([
            'user_id' => $user->id,
            'type' => 'lost',
            'title' => 'Barang Hilang Saya',
        ]);

        // 2. Create claim made by user on other user's item
        $otherItem = Item::factory()->create([
            'user_id' => $otherUser->id,
            'type' => 'found',
        ]);
        $claim = Claim::factory()->create([
            'claimer_id' => $user->id,
            'item_id' => $otherItem->id,
            'status' => 'pending',
        ]);

        // 3. Create claim received on user's item
        $incomingClaim = Claim::factory()->create([
            'claimer_id' => $otherUser->id,
            'item_id' => $item->id,
            'status' => 'pending',
        ]);

        // 4. Create returned item by user
        $returnedItem = Item::factory()->create([
            'user_id' => $user->id,
            'status' => 'returned',
            'title' => 'Barang Kembali',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/activities');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Riwayat aktivitas berhasil diambil',
            ]);

        $activities = $response->json('data.activities');
        $this->assertNotEmpty($activities);

        // Check that different types of activities are present
        $types = collect($activities)->pluck('type')->toArray();
        $this->assertContains('item_reported', $types);
        $this->assertContains('claim_made', $types);
        $this->assertContains('claim_received', $types);
        $this->assertContains('item_returned', $types);
    }
}
