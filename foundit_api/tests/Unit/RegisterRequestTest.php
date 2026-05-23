<?php

namespace Tests\Unit;

use App\Http\Requests\Auth\RegisterRequest;
use PHPUnit\Framework\TestCase;

/**
 * Unit Test Suite: RegisterRequestTest
 * Skenario Utama: Validasi rules dan messages pada RegisterRequest
 */
class RegisterRequestTest extends TestCase
{
    private RegisterRequest $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new RegisterRequest();
    }

    /**
     * TC-RR01: Memastikan request selalu authorized
     */
    public function test_authorize_returns_true(): void
    {
        $this->assertTrue($this->request->authorize());
    }

    /**
     * TC-RR02: Memastikan name field wajib diisi
     */
    public function test_name_field_is_required(): void
    {
        $rules = $this->request->rules();
        $this->assertArrayHasKey('name', $rules);
        $this->assertContains('required', $rules['name']);
    }

    /**
     * TC-RR03: Memastikan name field max 255 karakter
     */
    public function test_name_field_has_max_length(): void
    {
        $rules = $this->request->rules();
        $this->assertContains('max:255', $rules['name']);
    }

    /**
     * TC-RR04: Memastikan email field wajib dan unique
     */
    public function test_email_field_is_required_and_unique(): void
    {
        $rules = $this->request->rules();
        $this->assertArrayHasKey('email', $rules);
        $this->assertContains('required', $rules['email']);
        $this->assertContains('unique:users', $rules['email']);
    }

    /**
     * TC-RR05: Memastikan password field wajib dan minimal 8 karakter
     */
    public function test_password_field_is_required_with_min_length(): void
    {
        $rules = $this->request->rules();
        $this->assertArrayHasKey('password', $rules);
        $this->assertContains('required', $rules['password']);
        $this->assertContains('min:8', $rules['password']);
    }

    /**
     * TC-RR06: Memastikan password harus dikonfirmasi
     */
    public function test_password_field_requires_confirmation(): void
    {
        $rules = $this->request->rules();
        $this->assertContains('confirmed', $rules['password']);
    }

    /**
     * TC-RR07: Memastikan phone field wajib diisi
     */
    public function test_phone_field_is_required(): void
    {
        $rules = $this->request->rules();
        $this->assertArrayHasKey('phone', $rules);
        $this->assertContains('required', $rules['phone']);
    }

    /**
     * TC-RR08: Memastikan prodi_unit field wajib diisi
     */
    public function test_prodi_unit_field_is_required(): void
    {
        $rules = $this->request->rules();
        $this->assertArrayHasKey('prodi_unit', $rules);
        $this->assertContains('required', $rules['prodi_unit']);
    }

    /**
     * TC-RR09: Memastikan email harus domain UISI
     */
    public function test_email_must_match_uisi_domain(): void
    {
        $rules = $this->request->rules();
        $hasRegex = false;
        foreach ($rules['email'] as $rule) {
            if (is_string($rule) && str_starts_with($rule, 'regex:')) {
                $hasRegex = true;
                $this->assertStringContainsString('uisi', $rule);
                $this->assertStringContainsString('ac', $rule);
            }
        }
        $this->assertTrue($hasRegex);
    }

    /**
     * TC-RR10: Memastikan semua custom error messages terdefinisi
     */
    public function test_all_custom_messages_are_defined(): void
    {
        $messages = $this->request->messages();

        $expectedKeys = [
            'name.required', 'email.required', 'email.email',
            'email.unique', 'email.regex', 'password.required',
            'password.min', 'password.confirmed',
            'phone.required', 'prodi_unit.required',
        ];

        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, $messages, "Missing message for: $key");
        }
    }
}
