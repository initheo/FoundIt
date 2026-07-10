<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;

/**
 * Unit Test Suite: UserModelTest
 * Skenario Utama: Validasi struktur, atribut, dan logika model User
 */
class UserModelTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = new User();
    }

    /**
     * TC-U01: Memastikan fillable attributes User sesuai spesifikasi
     */
    public function test_fillable_attributes_are_correctly_defined(): void
    {
        $expected = ['name', 'email', 'password', 'phone', 'prodi_unit', 'photo_url', 'role'];
        $this->assertEquals($expected, $this->user->getFillable());
    }

    /**
     * TC-U02: Memastikan password disembunyikan dari serialisasi
     */
    public function test_password_is_hidden_from_serialization(): void
    {
        $this->assertContains('password', $this->user->getHidden());
    }

    /**
     * TC-U03: Memastikan remember_token disembunyikan dari serialisasi
     */
    public function test_remember_token_is_hidden_from_serialization(): void
    {
        $this->assertContains('remember_token', $this->user->getHidden());
    }

    /**
     * TC-U04: Memastikan email_verified_at di-cast ke datetime
     */
    public function test_email_verified_at_is_cast_to_datetime(): void
    {
        $casts = $this->user->getCasts();
        $this->assertArrayHasKey('email_verified_at', $casts);
        $this->assertEquals('datetime', $casts['email_verified_at']);
    }

    /**
     * TC-U05: Memastikan password di-cast sebagai hashed
     */
    public function test_password_is_cast_as_hashed(): void
    {
        $casts = $this->user->getCasts();
        $this->assertArrayHasKey('password', $casts);
        $this->assertEquals('hashed', $casts['password']);
    }

    /**
     * TC-U06: Memastikan relasi items() terdefinisi
     */
    public function test_has_items_relationship_method(): void
    {
        $this->assertTrue(method_exists($this->user, 'items'));
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $this->user->items());
    }

    /**
     * TC-U07: Memastikan relasi claims() terdefinisi
     */
    public function test_has_claims_relationship_method(): void
    {
        $this->assertTrue(method_exists($this->user, 'claims'));
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $this->user->claims());
    }

    /**
     * TC-U08: Memastikan User menggunakan HasApiTokens trait
     */
    public function test_uses_has_api_tokens_trait(): void
    {
        $traits = class_uses_recursive(User::class);
        $this->assertContains(\Laravel\Sanctum\HasApiTokens::class, $traits);
    }

    /**
     * TC-U09: Memastikan User menggunakan HasFactory trait
     */
    public function test_uses_has_factory_trait(): void
    {
        $traits = class_uses_recursive(User::class);
        $this->assertContains(\Illuminate\Database\Eloquent\Factories\HasFactory::class, $traits);
    }

    /**
     * TC-U10: Memastikan User menggunakan Notifiable trait
     */
    public function test_uses_notifiable_trait(): void
    {
        $traits = class_uses_recursive(User::class);
        $this->assertContains(\Illuminate\Notifications\Notifiable::class, $traits);
    }
}
