<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * @group Items
 *
 * API untuk mengelola laporan barang hilang dan temuan
 */
class ItemController extends Controller
{
    /**
     * List All Items
     *
     * Mengambil daftar semua item (barang hilang dan temuan).
     * Mendukung filter berdasarkan type (lost/found), kategori, dan pencarian.
     *
     * @queryParam type string Filter berdasarkan tipe. Example: lost
     * @queryParam category_id integer Filter berdasarkan kategori. Example: 1
     * @queryParam search string Pencarian berdasarkan judul, deskripsi, atau lokasi. Example: iPhone
     */
    public function index(Request $request)
    {
        $query = Item::with(['user:id,name,phone,photo_url', 'category:id,name', 'photos'])
            ->where('status', '!=', 'returned');

        // Filter by type (lost/found)
        if ($request->has('type') && in_array($request->type, ['lost', 'found'])) {
            $query->where('type', $request->type);
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Search by title or description (case-insensitive for PostgreSQL)
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(title) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(description) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(location) LIKE ?', ['%' . strtolower($search) . '%']);
            });
        }

        // Order by newest post first (created_at)
        $items = $query->orderBy('created_at', 'desc')->get();

        // Add photo_url and photo_urls to each item
        $items->transform(function ($item) {
            // Single photo for backward compatibility
            $item->photo_url = $item->photos->first()?->photo_url
                ? url(Storage::url($item->photos->first()->photo_url))
                : null;

            // All photos array with IDs for editing
            $item->photo_urls = $item->photos->map(function ($photo) {
                return [
                    'id' => $photo->id,
                    'url' => url(Storage::url($photo->photo_url)),
                ];
            })->toArray();

            return $item;
        });

        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }

    /**
     * Get Items Statistics
     *
     * Mengambil statistik jumlah item berdasarkan type dan status.
     */
    public function statistics()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'lost' => Item::where('type', 'lost')->where('status', '!=', 'returned')->count(),
                'found' => Item::where('type', 'found')->where('status', '!=', 'returned')->count(),
                'returned' => Item::where('status', 'returned')->count(),
            ],
        ]);
    }

    /**
     * Get Item Detail
     *
     * Mengambil detail lengkap dari satu item beserta foto dan klaim.
     *
     * @urlParam id integer required ID dari item. Example: 1
     */
    public function show($id)
    {
        $item = Item::with(['user:id,name,phone,email,prodi_unit,photo_url', 'category:id,name', 'photos', 'claims'])
            ->find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Item tidak ditemukan',
            ], 404);
        }

        // Transform photo URLs with full paths
        $item->photo_url = $item->photos->first()?->photo_url
            ? url(Storage::url($item->photos->first()->photo_url))
            : null;
        
        // Add photo_urls array with IDs for editing
        $item->photo_urls = $item->photos->map(function ($photo) {
            return [
                'id' => $photo->id,
                'url' => url(Storage::url($photo->photo_url)),
            ];
        })->toArray();

        return response()->json([
            'success' => true,
            'data' => $item,
        ]);
    }

    /**
     * Create Item
     *
     * Membuat laporan barang hilang atau temuan baru.
     *
     * @bodyParam type string required Tipe laporan: lost atau found. Example: found
     * @bodyParam category_id integer required ID kategori barang. Example: 1
     * @bodyParam title string required Judul/nama barang. Example: iPhone 15 Pro Max
     * @bodyParam description string required Deskripsi detail barang. Example: iPhone warna hitam dengan case bening
     * @bodyParam location string required Lokasi ditemukan/hilang. Example: Gedung A, Lt. 2
     * @bodyParam location_detail string Detail lokasi (label kustom). Example: Ruang CM201
     * @bodyParam date_time string required Tanggal dan waktu kejadian. Example: 2026-01-01 10:00:00
     * @bodyParam storage_info string Lokasi penyimpanan (untuk type=found). Example: Satpam Gedung B
     * @bodyParam photos file[] Foto barang (max 3, max 2MB each).
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:lost,found',
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|min:5|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'location_detail' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'date_time' => 'required|date',
            'storage_info' => 'nullable|string|max:100',
            'photos' => 'nullable|array|max:3',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'photos.*.max' => 'Ukuran foto maksimal 2MB',
            'photos.*.image' => 'File harus berupa gambar',
            'photos.*.mimes' => 'Format foto harus jpeg, png, atau jpg',
            'photos.max' => 'Maksimal 3 foto per item',
            'title.required' => 'Judul barang wajib diisi',
            'title.min' => 'Judul barang minimal 5 karakter',
            'description.required' => 'Deskripsi wajib diisi',
            'location.required' => 'Lokasi wajib diisi',
            'date_time.required' => 'Tanggal & waktu wajib diisi',
        ]);

        $item = Item::create([
            'user_id' => $request->user()->id,
            'category_id' => $request->category_id,
            'type' => $request->type,
            'title' => $request->title,
            'description' => $request->description,
            'location' => $request->location,
            'location_detail' => $request->location_detail,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'date_time' => $request->date_time,
            'storage_info' => $request->storage_info,
            'status' => 'active',
        ]);

        // Upload photos if provided
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('items', 'public');
                $item->photos()->create(['photo_url' => $path]);
            }
        }

        $item->load(['user:id,name,phone,photo_url', 'category:id,name', 'photos']);

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil dibuat',
            'data' => $item,
        ], 201);
    }

    /**
     * Update Item
     *
     * Mengupdate data item. Hanya pemilik item yang bisa mengupdate.
     *
     * @urlParam id integer required ID dari item. Example: 1
     * @bodyParam category_id integer ID kategori baru. Example: 2
     * @bodyParam title string Judul baru. Example: iPhone 15 Pro
     * @bodyParam description string Deskripsi baru.
     * @bodyParam location string Lokasi baru.
     * @bodyParam date_time string Tanggal waktu baru.
     * @bodyParam storage_info string Lokasi penyimpanan baru.
     */
    public function update(Request $request, $id)
    {
        $item = Item::where('user_id', $request->user()->id)->find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Item tidak ditemukan atau bukan milik Anda',
            ], 404);
        }

        $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'location' => 'sometimes|string|max:255',
            'location_detail' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'date_time' => 'sometimes|date',
            'storage_info' => 'nullable|string|max:100',
        ]);

        $item->update($request->only([
            'category_id',
            'title',
            'description',
            'location',
            'location_detail',
            'latitude',
            'longitude',
            'date_time',
            'storage_info'
        ]));

        $item->load(['user:id,name,phone,photo_url', 'category:id,name', 'photos']);
        
        // Transform photo URLs with full paths
        $item->photo_url = $item->photos->first()?->photo_url
            ? url(Storage::url($item->photos->first()->photo_url))
            : null;
        
        // Add photo_urls array with IDs for editing
        $item->photo_urls = $item->photos->map(function ($photo) {
            return [
                'id' => $photo->id,
                'url' => url(Storage::url($photo->photo_url)),
            ];
        })->toArray();

        return response()->json([
            'success' => true,
            'message' => 'Item berhasil diupdate',
            'data' => $item,
        ]);
    }

    /**
     * Delete Item
     *
     * Menghapus item. Hanya pemilik item yang bisa menghapus.
     * Foto yang terkait juga akan dihapus dari storage.
     *
     * @urlParam id integer required ID dari item. Example: 1
     */
    public function destroy(Request $request, $id)
    {
        $item = Item::where('user_id', $request->user()->id)->find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Item tidak ditemukan atau bukan milik Anda',
            ], 404);
        }

        // Delete photos from storage
        foreach ($item->photos as $photo) {
            Storage::disk('public')->delete($photo->photo_url);
        }

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item berhasil dihapus',
        ]);
    }

    /**
     * My Items
     *
     * Mengambil daftar item milik user yang sedang login.
     * Termasuk jumlah klaim yang masuk untuk setiap item.
     */
    public function myItems(Request $request)
    {
        $items = Item::with(['user:id,name,prodi_unit,photo_url', 'category:id,name', 'photos', 'claims'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $items->transform(function ($item) {
            $item->photo_url = $item->photos->first()?->photo_url
                ? url(Storage::url($item->photos->first()->photo_url))
                : null;
            
            // Add photo_urls array with IDs for editing
            $item->photo_urls = $item->photos->map(function ($photo) {
                return [
                    'id' => $photo->id,
                    'url' => url(Storage::url($photo->photo_url)),
                ];
            })->toArray();
            
            $item->claims_count = $item->claims->count();
            return $item;
        });

        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }

    /**
     * Update Item Status
     *
     * Mengupdate status item (misalnya: returned).
     * Untuk barang temuan (found): hanya pemilik/poster yang bisa update.
     * Untuk barang hilang (lost): approved claimer (penemu) yang bisa update.
     *
     * @urlParam id integer required ID dari item. Example: 1
     * @bodyParam status string required Status baru: active atau returned. Example: returned
     */
    public function updateStatus(Request $request, $id)
    {
        $item = Item::with('claims')->find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Item tidak ditemukan',
            ], 404);
        }

        $userId = $request->user()->id;
        $isOwner = $item->user_id === $userId;
        
        // Check if user is approved claimer for this item
        $isApprovedClaimer = $item->claims()
            ->where('claimer_id', $userId)
            ->where('status', 'approved')
            ->exists();

        // Authorization logic:
        // - Found items: only owner can mark as returned
        // - Lost items: only approved claimer (finder) can mark as returned
        $canUpdate = false;
        if ($item->type === 'found' && $isOwner) {
            $canUpdate = true;
        } elseif ($item->type === 'lost' && $isApprovedClaimer) {
            $canUpdate = true;
        }

        if (!$canUpdate) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk mengupdate status item ini',
            ], 403);
        }

        $request->validate([
            'status' => 'required|in:active,returned',
        ]);

        $item->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diupdate',
            'data' => $item,
        ]);
    }

    /**
     * Add Photo to Item
     *
     * Menambah foto baru ke item. Maksimal 3 foto per item.
     *
     * @urlParam id integer required ID dari item. Example: 1
     * @bodyParam photo file required Foto barang (max 2MB, jpeg/png/jpg).
     */
    public function addPhoto(Request $request, $id)
    {
        $item = Item::where('user_id', $request->user()->id)->find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Item tidak ditemukan atau bukan milik Anda',
            ], 404);
        }

        // Check if already has 3 photos
        if ($item->photos()->count() >= 3) {
            return response()->json([
                'success' => false,
                'message' => 'Maksimal 3 foto per item',
            ], 422);
        }

        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'photo.required' => 'Foto wajib diupload',
            'photo.image' => 'File harus berupa gambar',
            'photo.mimes' => 'Format foto harus jpeg, png, atau jpg',
            'photo.max' => 'Ukuran foto maksimal 2MB',
        ]);

        $path = $request->file('photo')->store('items', 'public');
        $photo = $item->photos()->create(['photo_url' => $path]);

        return response()->json([
            'success' => true,
            'message' => 'Foto berhasil ditambahkan',
            'data' => [
                'id' => $photo->id,
                'photo_url' => '/storage/' . $path,
            ],
        ]);
    }

    /**
     * Delete Photo from Item
     *
     * Menghapus foto dari item. Hanya pemilik item yang bisa menghapus.
     *
     * @urlParam id integer required ID dari item. Example: 1
     * @urlParam photoId integer required ID dari foto. Example: 5
     */
    public function deletePhoto(Request $request, $id, $photoId)
    {
        $item = Item::where('user_id', $request->user()->id)->find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Item tidak ditemukan atau bukan milik Anda',
            ], 404);
        }

        $photo = $item->photos()->find($photoId);

        if (!$photo) {
            return response()->json([
                'success' => false,
                'message' => 'Foto tidak ditemukan',
            ], 404);
        }

        // Delete from storage
        Storage::disk('public')->delete($photo->photo_url);
        $photo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Foto berhasil dihapus',
        ]);
    }
}
