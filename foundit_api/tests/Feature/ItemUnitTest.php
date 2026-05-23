<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ItemUnitTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /**
     * UT-01: Menambahkan item baru dengan data lengkap (Happy Path).
     */
    public function test_ut_01_store_item_successfully(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $data = [
            'type' => 'found',
            'category_id' => $category->id,
            'title' => 'Kunci Mobil BMW',
            'description' => 'Ditemukan di parkiran motor depan gedung A',
            'location' => 'Gedung A',
            'date_time' => '2026-01-01 10:00:00',
        ];

        $response = $this->actingAs($user)->postJson('/api/items', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id', 'title', 'type', 'user_id'
                ]
            ]);

        $this->assertDatabaseHas('items', [
            'title' => 'Kunci Mobil BMW',
            'user_id' => $user->id,
        ]);
    }

    /**
     * UT-02: Menambahkan item tanpa judul (Negative Path - Validation).
     */
    public function test_ut_02_store_item_validation_error(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $data = [
            'type' => 'found',
            'category_id' => $category->id,
            // 'title' is missing
            'description' => 'Ditemukan kunci',
            'location' => 'Gedung A',
            'date_time' => '2026-01-01 10:00:00',
        ];

        $response = $this->actingAs($user)->postJson('/api/items', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    /**
     * UT-03: Mengambil detail item yang ada (Happy Path).
     */
    public function test_ut_03_show_item_successfully(): void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)->getJson("/api/items/{$item->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $item->id,
                    'title' => $item->title,
                ]
            ]);
    }

    /**
     * UT-04: Mengambil detail item yang tidak ada (Negative Path - 404).
     */
    public function test_ut_04_show_item_not_found(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->getJson('/api/items/9999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Item tidak ditemukan',
            ]);
    }

    /**
     * UT-05: Menghapus item milik sendiri (Happy Path).
     */
    public function test_ut_05_destroy_item_successfully(): void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->deleteJson("/api/items/{$item->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Item berhasil dihapus',
            ]);

        $this->assertDatabaseMissing('items', ['id' => $item->id]);
    }

    /**
     * UT-06 (Modifikasi): Store item dengan judul < 5 karakter (Harus Error).
     */
    public function test_ut_06_store_item_title_min_length_validation(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $data = [
            'type' => 'found',
            'category_id' => $category->id,
            'title' => 'ABC', // < 5 chars
            'description' => 'Ditemukan kunci',
            'location' => 'Gedung A',
            'date_time' => '2026-01-01 10:00:00',
        ];

        $response = $this->actingAs($user)->postJson('/api/items', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    /**
     * UT-07 (Boundary): Store item dengan tepat 3 foto (Batas Maksimal).
     */
    public function test_ut_07_store_item_boundary_max_photos_success(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $data = [
            'type' => 'found',
            'category_id' => $category->id,
            'title' => 'Barang dengan 3 foto',
            'description' => 'Deskripsi barang',
            'location' => 'Gedung B',
            'date_time' => '2026-01-01 10:00:00',
            'photos' => [
                UploadedFile::fake()->image('photo1.jpg'),
                UploadedFile::fake()->image('photo2.jpg'),
                UploadedFile::fake()->image('photo3.jpg'),
            ]
        ];

        $response = $this->actingAs($user)->postJson('/api/items', $data);

        $response->assertStatus(201);
        $this->assertCount(3, Item::latest()->first()->photos);
    }

    /**
     * UT-08 (Boundary): Store item dengan 4 foto (Melewati Batas).
     */
    public function test_ut_08_store_item_boundary_max_photos_fail(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $data = [
            'type' => 'found',
            'category_id' => $category->id,
            'title' => 'Barang dengan 4 foto',
            'description' => 'Deskripsi barang',
            'location' => 'Gedung B',
            'date_time' => '2026-01-01 10:00:00',
            'photos' => [
                UploadedFile::fake()->image('photo1.jpg'),
                UploadedFile::fake()->image('photo2.jpg'),
                UploadedFile::fake()->image('photo3.jpg'),
                UploadedFile::fake()->image('photo4.jpg'),
            ]
        ];

        $response = $this->actingAs($user)->postJson('/api/items', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['photos']);
    }
}
