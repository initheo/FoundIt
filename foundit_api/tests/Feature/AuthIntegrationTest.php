<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Integration Test Suite: AuthIntegrationTest
 * Skenario Utama: Menguji alur registrasi, login, dan logout secara end-to-end
 */
class AuthIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * IT-AUTH01: Registrasi berhasil dengan data valid dan mendapat token
     */
    public function test_register_success_with_valid_data(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Budi Santoso',
            'email' => 'budi@student.uisi.ac.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '081234567890',
            'prodi_unit' => 'Teknik Informatika',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['user' => ['id', 'name', 'email'], 'token', 'token_type'],
            ])
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.email', 'budi@student.uisi.ac.id');

        $this->assertDatabaseHas('users', ['email' => 'budi@student.uisi.ac.id']);
    }

    /**
     * IT-AUTH02: Registrasi gagal dengan email non-UISI
     */
    public function test_register_fails_with_non_uisi_email(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Budi',
            'email' => 'budi@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '081234567890',
            'prodi_unit' => 'Teknik Informatika',
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseMissing('users', ['email' => 'budi@gmail.com']);
    }

    /**
     * IT-AUTH03: Registrasi gagal dengan email yang sudah terdaftar
     */
    public function test_register_fails_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'budi@student.uisi.ac.id']);

        $response = $this->postJson('/api/register', [
            'name' => 'Budi Lain',
            'email' => 'budi@student.uisi.ac.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '081234567890',
            'prodi_unit' => 'Teknik Informatika',
        ]);

        $response->assertStatus(422);
    }

    /**
     * IT-AUTH04: Login berhasil dengan kredensial valid
     */
    public function test_login_success_with_valid_credentials(): void
    {
        User::factory()->create([
            'email' => 'siti@student.uisi.ac.id',
            'password' => bcrypt('rahasia123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'siti@student.uisi.ac.id',
            'password' => 'rahasia123',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['token']]);
    }

    /**
     * IT-AUTH05: Login gagal dengan password salah
     */
    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->create([
            'email' => 'siti@student.uisi.ac.id',
            'password' => bcrypt('rahasia123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'siti@student.uisi.ac.id',
            'password' => 'salahpassword',
        ]);

        $response->assertStatus(401)
            ->assertJsonPath('success', false);
    }

    /**
     * IT-AUTH06: Login gagal dengan email yang tidak terdaftar
     */
    public function test_login_fails_with_unregistered_email(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'tidakada@student.uisi.ac.id',
            'password' => 'password123',
        ]);

        $response->assertStatus(401);
    }

    /**
     * IT-AUTH07: Logout berhasil dan token dihapus dari database
     */
    public function test_logout_success_and_token_deleted(): void
    {
        // Buat user dan login untuk mendapat real token
        User::factory()->create([
            'email' => 'test@student.uisi.ac.id',
            'password' => bcrypt('password123'),
        ]);

        $loginResponse = $this->postJson('/api/login', [
            'email' => 'test@student.uisi.ac.id',
            'password' => 'password123',
        ]);
        $token = $loginResponse->json('data.token');

        // Pastikan token ada di database
        $this->assertDatabaseCount('personal_access_tokens', 1);

        // Logout dengan real token
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        // Token sudah dihapus dari database
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    /**
     * IT-AUTH08: Akses endpoint protected tanpa token ditolak
     */
    public function test_protected_endpoint_requires_authentication(): void
    {
        $response = $this->getJson('/api/profile');
        $response->assertStatus(401);
    }

    /**
     * IT-AUTH09: Registrasi gagal dengan nomor HP yang sudah terdaftar
     */
    public function test_register_fails_with_duplicate_phone(): void
    {
        User::factory()->create(['phone' => '081234567890']);

        $response = $this->postJson('/api/register', [
            'name' => 'Budi Baru',
            'email' => 'budibaru@student.uisi.ac.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '081234567890',
            'prodi_unit' => 'Teknik Informatika',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['phone']);
    }

    /**
     * IT-AUTH10: Registrasi berhasil dan nomor HP +62 dinormalisasi ke 0
     */
    public function test_register_normalizes_international_phone(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Budi International',
            'email' => 'budi_int@student.uisi.ac.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '+6281234567890',
            'prodi_unit' => 'Teknik Informatika',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'email' => 'budi_int@student.uisi.ac.id',
            'phone' => '081234567890'
        ]);
    }

    /**
     * IT-AUTH11: Mengirim request POST dengan header Content-Type text/plain ditolak dengan HTTP 415
     */
    public function test_unsupported_content_type_returns_415(): void
    {
        $response = $this->withHeaders([
            'Content-Type' => 'text/plain',
        ])->post('/api/register', [
            'name' => 'Budi Error',
            'email' => 'budi_error@student.uisi.ac.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '081234567891',
            'prodi_unit' => 'Teknik Informatika',
        ]);

        $response->assertStatus(415)
            ->assertJson([
                'success' => false,
                'message' => 'Unsupported Media Type',
            ]);
    }

    /**
     * IT-AUTH12: Login rate limiting limits attempts to 10 per minute
     */
    public function test_login_rate_limiting_works(): void
    {
        User::factory()->create([
            'email' => 'siti2@student.uisi.ac.id',
            'password' => bcrypt('rahasia123'),
        ]);

        for ($i = 0; $i < 10; $i++) {
            $response = $this->postJson('/api/login', [
                'email' => 'siti2@student.uisi.ac.id',
                'password' => 'wrong-password',
            ]);
            $response->assertStatus(401);
        }

        $response = $this->postJson('/api/login', [
            'email' => 'siti2@student.uisi.ac.id',
            'password' => 'wrong-password',
        ]);
        $response->assertStatus(429);
    }
}
