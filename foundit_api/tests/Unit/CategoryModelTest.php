<?php

namespace Tests\Unit;

use App\Models\Category;
use PHPUnit\Framework\TestCase;

/**
 * Unit Test Suite: CategoryModelTest
 * Skenario Utama: Validasi struktur, atribut, dan relasi model Category
 */
class CategoryModelTest extends TestCase
{
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = new Category();
    }

    /**
     * TC-CAT01: Memastikan fillable attributes Category sesuai spesifikasi
     */
    public function test_fillable_attributes_are_correctly_defined(): void
    {
        $expected = ['name', 'icon'];
        $this->assertEquals($expected, $this->category->getFillable());
    }

    /**
     * TC-CAT02: Memastikan relasi items() terdefinisi
     */
    public function test_has_items_relationship_method(): void
    {
        $this->assertTrue(method_exists($this->category, 'items'));
    }

    /**
     * TC-CAT03: Memastikan Category menggunakan HasFactory trait
     */
    public function test_uses_has_factory_trait(): void
    {
        $traits = class_uses_recursive(Category::class);
        $this->assertContains(\Illuminate\Database\Eloquent\Factories\HasFactory::class, $traits);
    }

    /**
     * TC-CAT04: Memastikan tabel default sesuai konvensi (categories)
     */
    public function test_table_name_follows_convention(): void
    {
        $this->assertEquals('categories', $this->category->getTable());
    }

    /**
     * TC-CAT05: Memastikan tidak ada hidden attributes (semua data publik)
     */
    public function test_no_hidden_attributes(): void
    {
        $this->assertEmpty($this->category->getHidden());
    }
}
