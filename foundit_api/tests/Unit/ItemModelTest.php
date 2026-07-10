<?php

namespace Tests\Unit;

use App\Models\Item;
use Tests\TestCase;

/**
 * Unit Test Suite: ItemModelTest
 * Skenario Utama: Validasi struktur, atribut, casting, scope, dan relasi model Item
 */
class ItemModelTest extends TestCase
{
    private Item $item;

    protected function setUp(): void
    {
        parent::setUp();
        $this->item = new Item();
    }

    /**
     * TC-I01: Memastikan fillable attributes Item sesuai spesifikasi
     */
    public function test_fillable_attributes_are_correctly_defined(): void
    {
        $expected = [
            'user_id', 'category_id', 'type', 'title', 'description',
            'location', 'location_detail', 'latitude', 'longitude',
            'date_time', 'storage_info', 'status',
        ];
        $this->assertEquals($expected, $this->item->getFillable());
    }

    /**
     * TC-I02: Memastikan date_time di-cast ke datetime
     */
    public function test_date_time_is_cast_to_datetime(): void
    {
        $casts = $this->item->getCasts();
        $this->assertArrayHasKey('date_time', $casts);
        $this->assertEquals('datetime', $casts['date_time']);
    }

    /**
     * TC-I03: Memastikan serializeDate menghasilkan format Y-m-d H:i:s
     */
    public function test_serialize_date_returns_correct_format(): void
    {
        $date = new \DateTime('2025-03-20 10:15:30');
        $reflection = new \ReflectionMethod($this->item, 'serializeDate');
        $result = $reflection->invoke($this->item, $date);

        $this->assertEquals('2025-03-20 10:15:30', $result);
    }

    /**
     * TC-I04: Memastikan relasi user() terdefinisi
     */
    public function test_has_user_relationship_method(): void
    {
        $this->assertTrue(method_exists($this->item, 'user'));
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $this->item->user());
    }

    /**
     * TC-I05: Memastikan relasi category() terdefinisi
     */
    public function test_has_category_relationship_method(): void
    {
        $this->assertTrue(method_exists($this->item, 'category'));
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $this->item->category());
    }

    /**
     * TC-I06: Memastikan relasi photos() terdefinisi
     */
    public function test_has_photos_relationship_method(): void
    {
        $this->assertTrue(method_exists($this->item, 'photos'));
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $this->item->photos());
    }

    /**
     * TC-I07: Memastikan relasi claims() terdefinisi
     */
    public function test_has_claims_relationship_method(): void
    {
        $this->assertTrue(method_exists($this->item, 'claims'));
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $this->item->claims());
    }

    /**
     * TC-I08: Memastikan scope scopeLost() terdefinisi
     */
    public function test_has_scope_lost_method(): void
    {
        $this->assertTrue(method_exists($this->item, 'scopeLost'));
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, Item::query()->lost());
    }

    /**
     * TC-I09: Memastikan scope scopeFound() terdefinisi
     */
    public function test_has_scope_found_method(): void
    {
        $this->assertTrue(method_exists($this->item, 'scopeFound'));
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, Item::query()->found());
    }

    /**
     * TC-I10: Memastikan scope scopeActive() terdefinisi
     */
    public function test_has_scope_active_method(): void
    {
        $this->assertTrue(method_exists($this->item, 'scopeActive'));
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, Item::query()->active());
    }

    /**
     * TC-I11: Memastikan Item menggunakan HasFactory trait
     */
    public function test_uses_has_factory_trait(): void
    {
        $traits = class_uses_recursive(Item::class);
        $this->assertContains(\Illuminate\Database\Eloquent\Factories\HasFactory::class, $traits);
    }

    /**
     * TC-I12: Memastikan tabel default sesuai konvensi (items)
     */
    public function test_table_name_follows_convention(): void
    {
        $this->assertEquals('items', $this->item->getTable());
    }
}
