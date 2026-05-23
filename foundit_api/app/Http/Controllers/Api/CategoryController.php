<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

/**
 * @group Categories
 *
 * API untuk mengambil daftar kategori barang
 */
class CategoryController extends Controller
{
    /**
     * List All Categories
     *
     * Mengambil daftar semua kategori yang tersedia.
     */
    public function index()
    {
        $categories = Category::orderBy('name')->get(['id', 'name']);

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }
}
