<?php

namespace Tests\Unit;

use App\Http\Requests\Auth\LoginRequest;
use PHPUnit\Framework\TestCase;

/**
 * Unit Test Suite: LoginRequestTest
 * Skenario Utama: Validasi rules dan messages pada LoginRequest
 */
class LoginRequestTest extends TestCase
{
    private LoginRequest $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new LoginRequest();
    }

    /**
     * TC-LR01: Memastikan request selalu authorized
     */
    public function test_authorize_returns_true(): void
    {
        $this->assertTrue($this->request->authorize());
    }

    /**
     * TC-LR02: Memastikan email field wajib diisi (required)
     */
    public function test_email_field_is_required(): void
    {
        $rules = $this->request->rules();
        $this->assertArrayHasKey('email', $rules);
        $this->assertContains('required', $rules['email']);
    }

    /**
     * TC-LR03: Memastikan email harus format email valid
     */
    public function test_email_field_must_be_valid_email(): void
    {
        $rules = $this->request->rules();
        $this->assertContains('email', $rules['email']);
    }

    /**
     * TC-LR04: Memastikan email harus domain UISI (regex)
     */
    public function test_email_field_must_match_uisi_domain_regex(): void
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
        $this->assertTrue($hasRegex, 'Email harus memiliki regex rule untuk domain UISI');
    }

    /**
     * TC-LR05: Memastikan password field wajib diisi
     */
    public function test_password_field_is_required(): void
    {
        $rules = $this->request->rules();
        $this->assertArrayHasKey('password', $rules);
        $this->assertContains('required', $rules['password']);
    }

    /**
     * TC-LR06: Memastikan custom messages terdefinisi untuk email.required
     */
    public function test_has_custom_message_for_email_required(): void
    {
        $messages = $this->request->messages();
        $this->assertArrayHasKey('email.required', $messages);
        $this->assertNotEmpty($messages['email.required']);
    }

    /**
     * TC-LR07: Memastikan custom messages terdefinisi untuk password.required
     */
    public function test_has_custom_message_for_password_required(): void
    {
        $messages = $this->request->messages();
        $this->assertArrayHasKey('password.required', $messages);
        $this->assertNotEmpty($messages['password.required']);
    }

    /**
     * TC-LR08: Memastikan custom messages terdefinisi untuk email.regex
     */
    public function test_has_custom_message_for_email_regex(): void
    {
        $messages = $this->request->messages();
        $this->assertArrayHasKey('email.regex', $messages);
        $this->assertStringContainsString('UISI', $messages['email.regex']);
    }
}
