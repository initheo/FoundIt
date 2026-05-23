<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Category;
use App\Models\User;
use App\Models\ItemPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemWebController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::with(['user', 'category', 'photos']);

        // Filter by type
        if ($request->filled('type') && in_array($request->type, ['lost', 'found'])) {
            $query->where('type', $request->type);
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search title, description, location
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(title) LIKE ?', ['%' . strtolower($search) . '%'])
                  ->orWhereRaw('LOWER(description) LIKE ?', ['%' . strtolower($search) . '%'])
                  ->orWhereRaw('LOWER(location) LIKE ?', ['%' . strtolower($search) . '%']);
            });
        }

        $items = $query->orderBy('created_at', 'desc')->paginate(10);
        $categories = Category::orderBy('name')->get();

        return view('admin.items.index', compact('items', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        // Get non-admin users for reporter dropdown
        $users = User::where('role', '!=', 'admin')->orderBy('name')->get();
        return view('admin.items.create', compact('categories', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:lost,found',
            'category_id' => 'required|exists:categories,id',
            'user_id' => 'required|exists:users,id',
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
        ]);

        $item = Item::create([
            'user_id' => $validated['user_id'],
            'category_id' => $validated['category_id'],
            'type' => $validated['type'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'location' => $validated['location'],
            'location_detail' => $validated['location_detail'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'date_time' => $validated['date_time'],
            'storage_info' => $validated['storage_info'] ?? null,
            'status' => 'active',
        ]);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('items', 'public');
                $item->photos()->create(['photo_url' => $path]);
            }
        }

        return redirect()->route('admin.items.index')
            ->with('success', 'Laporan barang berhasil dibuat.');
    }

    public function show($id)
    {
        $item = Item::with(['user', 'category', 'photos', 'claims.claimer'])->findOrFail($id);
        return view('admin.items.show', compact('item'));
    }

    public function edit($id)
    {
        $item = Item::with('photos')->findOrFail($id);
        $categories = Category::orderBy('name')->get();
        $users = User::where('role', '!=', 'admin')->orderBy('name')->get();
        return view('admin.items.edit', compact('item', 'categories', 'users'));
    }

    public function update(Request $request, $id)
    {
        $item = Item::findOrFail($id);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|min:5|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'location_detail' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'date_time' => 'required|date',
            'storage_info' => 'nullable|string|max:100',
            'status' => 'required|in:active,claimed,returned',
        ]);

        $item->update($validated);

        return redirect()->route('admin.items.show', $item->id)
            ->with('success', 'Laporan barang berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $item = Item::findOrFail($id);

        // Delete photos from storage
        foreach ($item->photos as $photo) {
            Storage::disk('public')->delete($photo->photo_url);
        }

        // Claims will be deleted cascade if defined in migration, or manually delete them
        $item->claims()->delete();
        $item->photos()->delete();
        $item->delete();

        return redirect()->route('admin.items.index')
            ->with('success', 'Laporan barang berhasil dihapus.');
    }

    public function addPhoto(Request $request, $id)
    {
        $item = Item::findOrFail($id);

        if ($item->photos()->count() >= 3) {
            return redirect()->back()->with('error', 'Maksimal 3 foto per item.');
        }

        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $path = $request->file('photo')->store('items', 'public');
        $item->photos()->create(['photo_url' => $path]);

        return redirect()->back()->with('success', 'Foto berhasil ditambahkan.');
    }

    public function deletePhoto(Request $request, $id, $photoId)
    {
        $item = Item::findOrFail($id);
        $photo = $item->photos()->findOrFail($photoId);

        Storage::disk('public')->delete($photo->photo_url);
        $photo->delete();

        return redirect()->back()->with('success', 'Foto berhasil dihapus.');
    }
}
