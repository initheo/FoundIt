<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Integration Test Suite: ItemCrudIntegrationTest
 * Skenario Utama: Menguji alur CRUD barang (buat, baca, update, hapus) secara end-to-end
 */
class ItemCrudIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->category = Category::factory()->create(['name' => 'Elektronik']);
    }

    /**
     * IT-ITEM01: Membuat laporan barang berhasil dengan data lengkap
     */
    public function test_create_item_success(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->user)->postJson('/api/items', [
            'type' => 'found',
            'category_id' => $this->category->id,
            'title' => 'iPhone 15 Pro ditemukan',
            'description' => 'iPhone warna hitam ditemukan di kantin',
            'location' => 'Kantin Gedung A',
            'date_time' => '2025-06-15 10:00:00',
            'storage_info' => 'Satpam Gedung A',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.title', 'iPhone 15 Pro ditemukan')
            ->assertJsonPath('data.status', 'active');

        $this->assertDatabaseHas('items', [
            'title' => 'iPhone 15 Pro ditemukan',
            'user_id' => $this->user->id,
        ]);
    }

    /**
     * IT-ITEM02: Membuat laporan gagal tanpa field wajib
     */
    public function test_create_item_fails_without_required_fields(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/items', [
            'type' => 'lost',
        ]);

        $response->assertStatus(422);
    }

    /**
     * IT-ITEM03: Melihat detail barang berhasil
     */
    public function test_show_item_detail(): void
    {
        $item = Item::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Dompet Hitam',
        ]);

        $response = $this->actingAs($this->user)->getJson("/api/items/{$item->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.title', 'Dompet Hitam');
    }

    /**
     * IT-ITEM04: Melihat detail barang yang tidak ada mengembalikan 404
     */
    public function test_show_nonexistent_item_returns_404(): void
    {
        $response = $this->actingAs($this->user)->getJson('/api/items/99999');
        $response->assertStatus(404);
    }

    /**
     * IT-ITEM05: Update barang oleh pemilik berhasil
     */
    public function test_update_item_by_owner_success(): void
    {
        $item = Item::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Judul Lama',
        ]);

        $response = $this->actingAs($this->user)->putJson("/api/items/{$item->id}", [
            'title' => 'Judul Baru',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'Judul Baru');

        $this->assertDatabaseHas('items', ['id' => $item->id, 'title' => 'Judul Baru']);
    }

    /**
     * IT-ITEM06: Update barang oleh bukan pemilik ditolak
     */
    public function test_update_item_by_non_owner_rejected(): void
    {
        $otherUser = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)->putJson("/api/items/{$item->id}", [
            'title' => 'Coba Edit',
        ]);

        $response->assertStatus(404);
    }

    /**
     * IT-ITEM07: Hapus barang oleh pemilik berhasil
     */
    public function test_delete_item_by_owner_success(): void
    {
        $item = Item::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->deleteJson("/api/items/{$item->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('items', ['id' => $item->id]);
    }

    /**
     * IT-ITEM08: Hapus barang oleh bukan pemilik ditolak
     */
    public function test_delete_item_by_non_owner_rejected(): void
    {
        $otherUser = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)->deleteJson("/api/items/{$item->id}");

        $response->assertStatus(404);
        $this->assertDatabaseHas('items', ['id' => $item->id]);
    }

    /**
     * IT-ITEM09: Upload foto ke barang berhasil
     */
    public function test_add_photo_to_item_success(): void
    {
        Storage::fake('public');
        $item = Item::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->postJson("/api/items/{$item->id}/photos", [
            'photo' => UploadedFile::fake()->image('barang.jpg'),
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseCount('item_photos', 1);
    }

    /**
     * IT-ITEM10: Melihat daftar barang milik sendiri (My Items)
     */
    public function test_my_items_returns_only_own_items(): void
    {
        $otherUser = User::factory()->create();
        Item::factory()->count(3)->create(['user_id' => $this->user->id]);
        Item::factory()->count(2)->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)->getJson('/api/items/my');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }
}
