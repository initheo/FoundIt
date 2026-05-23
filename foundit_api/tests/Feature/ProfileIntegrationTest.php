<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Integration Test Suite: ProfileIntegrationTest
 * Skenario Utama: Menguji alur kelola profil pengguna (lihat, update, foto) secara end-to-end
 */
class ProfileIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'name' => 'Ahmad Fauzi',
            'email' => 'ahmad@student.uisi.ac.id',
            'phone' => '081234567890',
            'prodi_unit' => 'Teknik Informatika',
        ]);
    }

    /**
     * IT-PRF01: Melihat profil sendiri berhasil
     */
    public function test_get_own_profile_success(): void
    {
        $response = $this->actingAs($this->user)->getJson('/api/profile');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.name', 'Ahmad Fauzi')
            ->assertJsonPath('data.user.email', 'ahmad@student.uisi.ac.id');
    }

    /**
     * IT-PRF02: Update nama profil berhasil
     */
    public function test_update_profile_name_success(): void
    {
        $response = $this->actingAs($this->user)->putJson('/api/profile', [
            'name' => 'Ahmad Fauzi Baru',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.user.name', 'Ahmad Fauzi Baru');

        $this->assertDatabaseHas('users', ['id' => $this->user->id, 'name' => 'Ahmad Fauzi Baru']);
    }

    /**
     * IT-PRF03: Update email dengan domain non-UISI ditolak
     */
    public function test_update_email_non_uisi_rejected(): void
    {
        $response = $this->actingAs($this->user)->putJson('/api/profile', [
            'email' => 'ahmad@gmail.com',
        ]);

        $response->assertStatus(422);
    }

    /**
     * IT-PRF04: Update nomor HP berhasil
     */
    public function test_update_phone_success(): void
    {
        $response = $this->actingAs($this->user)->putJson('/api/profile', [
            'phone' => '089876543210',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.user.phone', '089876543210');
    }

    /**
     * IT-PRF05: Upload foto profil berhasil
     */
    public function test_upload_profile_photo_success(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->user)->postJson('/api/profile/photo', [
            'photo' => UploadedFile::fake()->image('profile.jpg'),
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['photo_url']]);
    }

    /**
     * IT-PRF06: Hapus foto profil berhasil
     */
    public function test_delete_profile_photo_success(): void
    {
        Storage::fake('public');
        $this->user->update(['photo_url' => '/storage/photos/profiles/test.jpg']);

        $response = $this->actingAs($this->user)->deleteJson('/api/profile/photo');

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertNull($this->user->refresh()->photo_url);
    }

    /**
     * IT-PRF07: Hapus foto profil gagal jika tidak ada foto
     */
    public function test_delete_photo_fails_when_no_photo(): void
    {
        $this->user->update(['photo_url' => null]);

        $response = $this->actingAs($this->user)->deleteJson('/api/profile/photo');

        $response->assertStatus(400);
    }

    /**
     * IT-PRF08: Update email ke email yang sudah dipakai user lain ditolak
     */
    public function test_update_email_to_existing_email_rejected(): void
    {
        User::factory()->create(['email' => 'siti@student.uisi.ac.id']);

        $response = $this->actingAs($this->user)->putJson('/api/profile', [
            'email' => 'siti@student.uisi.ac.id',
        ]);

        $response->assertStatus(422);
    }
}
