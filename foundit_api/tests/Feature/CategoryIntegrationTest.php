<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Integration Test Suite: CategoryIntegrationTest
 * Skenario Utama: Menguji endpoint kategori dan integrasinya dengan barang
 */
class CategoryIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * IT-CAT01: Mengambil daftar kategori berhasil
     */
    public function test_get_categories_success(): void
    {
        Category::factory()->count(5)->create();

        $response = $this->actingAs($this->user)->getJson('/api/categories');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(5, 'data');
    }

    /**
     * IT-CAT02: Daftar kategori diurutkan berdasarkan nama
     */
    public function test_categories_ordered_by_name(): void
    {
        Category::factory()->create(['name' => 'Tas']);
        Category::factory()->create(['name' => 'Elektronik']);
        Category::factory()->create(['name' => 'Dokumen']);

        $response = $this->actingAs($this->user)->getJson('/api/categories');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertEquals('Dokumen', $data[0]['name']);
        $this->assertEquals('Elektronik', $data[1]['name']);
        $this->assertEquals('Tas', $data[2]['name']);
    }

    /**
     * IT-CAT03: Kategori hanya mengembalikan id dan name
     */
    public function test_categories_return_only_id_and_name(): void
    {
        Category::factory()->create(['name' => 'Kunci', 'icon' => 'key_icon']);

        $response = $this->actingAs($this->user)->getJson('/api/categories');

        $response->assertStatus(200);
        $item = $response->json('data.0');
        $this->assertArrayHasKey('id', $item);
        $this->assertArrayHasKey('name', $item);
        $this->assertArrayNotHasKey('icon', $item);
    }

    /**
     * IT-CAT04: Endpoint kategori membutuhkan autentikasi
     */
    public function test_categories_requires_authentication(): void
    {
        $response = $this->getJson('/api/categories');
        $response->assertStatus(401);
    }

    /**
     * IT-CAT05: Daftar kategori kosong jika belum ada data
     */
    public function test_categories_returns_empty_when_no_data(): void
    {
        $response = $this->actingAs($this->user)->getJson('/api/categories');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(0, 'data');
    }
}
