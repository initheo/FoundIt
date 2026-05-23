<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Integration Test Suite: ItemFilterIntegrationTest
 * Skenario Utama: Menguji fitur pencarian, filter, dan statistik barang
 */
class ItemFilterIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * IT-FIL01: Filter barang berdasarkan tipe (lost)
     */
    public function test_filter_items_by_type_lost(): void
    {
        Item::factory()->count(3)->create(['type' => 'lost']);
        Item::factory()->count(2)->create(['type' => 'found']);

        $response = $this->actingAs($this->user)->getJson('/api/items?type=lost');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    /**
     * IT-FIL02: Filter barang berdasarkan tipe (found)
     */
    public function test_filter_items_by_type_found(): void
    {
        Item::factory()->count(3)->create(['type' => 'lost']);
        Item::factory()->count(2)->create(['type' => 'found']);

        $response = $this->actingAs($this->user)->getJson('/api/items?type=found');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    /**
     * IT-FIL03: Filter barang berdasarkan kategori
     */
    public function test_filter_items_by_category(): void
    {
        $catElektronik = Category::factory()->create(['name' => 'Elektronik']);
        $catDokumen = Category::factory()->create(['name' => 'Dokumen']);

        Item::factory()->count(4)->create(['category_id' => $catElektronik->id]);
        Item::factory()->count(2)->create(['category_id' => $catDokumen->id]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/items?category_id={$catElektronik->id}");

        $response->assertStatus(200)
            ->assertJsonCount(4, 'data');
    }

    /**
     * IT-FIL04: Pencarian barang berdasarkan judul
     */
    public function test_search_items_by_title(): void
    {
        Item::factory()->create(['title' => 'iPhone 15 Pro Max']);
        Item::factory()->create(['title' => 'Dompet Kulit Hitam']);
        Item::factory()->create(['title' => 'Kunci Motor Honda']);

        $response = $this->actingAs($this->user)->getJson('/api/items?search=iPhone');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'iPhone 15 Pro Max');
    }

    /**
     * IT-FIL05: Pencarian barang berdasarkan lokasi
     */
    public function test_search_items_by_location(): void
    {
        Item::factory()->create(['location' => 'Kantin Gedung A']);
        Item::factory()->create(['location' => 'Perpustakaan']);
        Item::factory()->create(['location' => 'Kantin Gedung B']);

        $response = $this->actingAs($this->user)->getJson('/api/items?search=Kantin');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    /**
     * IT-FIL06: Statistik barang menampilkan jumlah yang benar
     */
    public function test_statistics_returns_correct_counts(): void
    {
        Item::factory()->count(3)->create(['type' => 'lost', 'status' => 'active']);
        Item::factory()->count(2)->create(['type' => 'found', 'status' => 'active']);
        Item::factory()->count(1)->create(['type' => 'lost', 'status' => 'returned']);

        $response = $this->actingAs($this->user)->getJson('/api/items/stats');

        $response->assertStatus(200)
            ->assertJsonPath('data.lost', 3)
            ->assertJsonPath('data.found', 2)
            ->assertJsonPath('data.returned', 1);
    }

    /**
     * IT-FIL07: Daftar barang tidak menampilkan yang sudah returned
     */
    public function test_item_list_excludes_returned_items(): void
    {
        Item::factory()->count(3)->create(['status' => 'active']);
        Item::factory()->count(2)->create(['status' => 'returned']);

        $response = $this->actingAs($this->user)->getJson('/api/items');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    /**
     * IT-FIL08: Daftar barang diurutkan dari terbaru
     */
    public function test_items_ordered_by_newest_first(): void
    {
        $old = Item::factory()->create(['title' => 'Barang Lama', 'created_at' => now()->subDays(5)]);
        $new = Item::factory()->create(['title' => 'Barang Baru', 'created_at' => now()]);

        $response = $this->actingAs($this->user)->getJson('/api/items');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertEquals('Barang Baru', $data[0]['title']);
        $this->assertEquals('Barang Lama', $data[1]['title']);
    }
}
