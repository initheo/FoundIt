<?php

namespace Tests\Unit;

use App\Models\Claim;
use PHPUnit\Framework\TestCase;

/**
 * Unit Test Suite: ClaimModelTest
 * Skenario Utama: Validasi struktur, atribut, casting, scope, dan relasi model Claim
 */
class ClaimModelTest extends TestCase
{
    private Claim $claim;

    protected function setUp(): void
    {
        parent::setUp();
        $this->claim = new Claim();
    }

    /**
     * TC-C01: Memastikan fillable attributes Claim sesuai spesifikasi
     */
    public function test_fillable_attributes_are_correctly_defined(): void
    {
        $expected = [
            'item_id', 'claimer_id', 'reason', 'status',
            'rejection_reason', 'reviewed_at',
        ];
        $this->assertEquals($expected, $this->claim->getFillable());
    }

    /**
     * TC-C02: Memastikan reviewed_at di-cast ke datetime
     */
    public function test_reviewed_at_is_cast_to_datetime(): void
    {
        $casts = $this->claim->getCasts();
        $this->assertArrayHasKey('reviewed_at', $casts);
        $this->assertEquals('datetime', $casts['reviewed_at']);
    }

    /**
     * TC-C03: Memastikan serializeDate menghasilkan format Y-m-d H:i:s
     */
    public function test_serialize_date_returns_correct_format(): void
    {
        $date = new \DateTime('2025-07-01 08:00:00');
        $reflection = new \ReflectionMethod($this->claim, 'serializeDate');
        $result = $reflection->invoke($this->claim, $date);

        $this->assertEquals('2025-07-01 08:00:00', $result);
    }

    /**
     * TC-C04: Memastikan relasi item() terdefinisi
     */
    public function test_has_item_relationship_method(): void
    {
        $this->assertTrue(method_exists($this->claim, 'item'));
    }

    /**
     * TC-C05: Memastikan relasi claimer() terdefinisi
     */
    public function test_has_claimer_relationship_method(): void
    {
        $this->assertTrue(method_exists($this->claim, 'claimer'));
    }

    /**
     * TC-C06: Memastikan scope scopePending() terdefinisi
     */
    public function test_has_scope_pending_method(): void
    {
        $this->assertTrue(method_exists($this->claim, 'scopePending'));
    }

    /**
     * TC-C07: Memastikan scope scopeApproved() terdefinisi
     */
    public function test_has_scope_approved_method(): void
    {
        $this->assertTrue(method_exists($this->claim, 'scopeApproved'));
    }

    /**
     * TC-C08: Memastikan scope scopeRejected() terdefinisi
     */
    public function test_has_scope_rejected_method(): void
    {
        $this->assertTrue(method_exists($this->claim, 'scopeRejected'));
    }

    /**
     * TC-C09: Memastikan Claim menggunakan HasFactory trait
     */
    public function test_uses_has_factory_trait(): void
    {
        $traits = class_uses_recursive(Claim::class);
        $this->assertContains(\Illuminate\Database\Eloquent\Factories\HasFactory::class, $traits);
    }

    /**
     * TC-C10: Memastikan tabel default sesuai konvensi (claims)
     */
    public function test_table_name_follows_convention(): void
    {
        $this->assertEquals('claims', $this->claim->getTable());
    }
}
