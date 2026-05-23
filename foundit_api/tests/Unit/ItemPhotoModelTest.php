<?php

namespace Tests\Unit;

use App\Models\ItemPhoto;
use PHPUnit\Framework\TestCase;

/**
 * Unit Test Suite: ItemPhotoModelTest
 * Skenario Utama: Validasi struktur, atribut, dan relasi model ItemPhoto
 */
class ItemPhotoModelTest extends TestCase
{
    private ItemPhoto $photo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->photo = new ItemPhoto();
    }

    /**
     * TC-P01: Memastikan fillable attributes ItemPhoto sesuai spesifikasi
     */
    public function test_fillable_attributes_are_correctly_defined(): void
    {
        $expected = ['item_id', 'photo_url'];
        $this->assertEquals($expected, $this->photo->getFillable());
    }

    /**
     * TC-P02: Memastikan relasi item() terdefinisi
     */
    public function test_has_item_relationship_method(): void
    {
        $this->assertTrue(method_exists($this->photo, 'item'));
    }

    /**
     * TC-P03: Memastikan ItemPhoto menggunakan HasFactory trait
     */
    public function test_uses_has_factory_trait(): void
    {
        $traits = class_uses_recursive(ItemPhoto::class);
        $this->assertContains(\Illuminate\Database\Eloquent\Factories\HasFactory::class, $traits);
    }

    /**
     * TC-P04: Memastikan tabel default sesuai konvensi (item_photos)
     */
    public function test_table_name_follows_convention(): void
    {
        $this->assertEquals('item_photos', $this->photo->getTable());
    }

    /**
     * TC-P05: Memastikan tidak ada hidden attributes
     */
    public function test_no_hidden_attributes(): void
    {
        $this->assertEmpty($this->photo->getHidden());
    }
}
