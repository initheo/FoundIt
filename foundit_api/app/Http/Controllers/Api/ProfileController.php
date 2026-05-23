<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Requests\Profile\UploadPhotoRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * @tags Profile
 */
class ProfileController extends Controller
{
    /**
     * Get current user profile
     * 
     * Mendapatkan data profil user yang sedang login.
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diambil',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'prodi_unit' => $user->prodi_unit,
                    'photo_url' => $user->photo_url,
                    'created_at' => $user->created_at,
                ],
            ],
        ], 200);
    }

    /**
     * Update current user profile
     * 
     * Mengupdate data profil user yang sedang login.
     * Email tetap wajib menggunakan domain UISI (@student.uisi.ac.id atau @uisi.ac.id).
     * Semua field bersifat opsional, hanya field yang dikirim yang akan diupdate.
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();

        $data = $request->only([
            'name',
            'email',
            'phone',
            'prodi_unit',
            'photo_url',
        ]);

        if (isset($data['email'])) {
            $data['email'] = strtolower($data['email']);
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diupdate',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'prodi_unit' => $user->prodi_unit,
                    'photo_url' => $user->photo_url,
                    'created_at' => $user->created_at,
                ],
            ],
        ], 200);
    }

    /**
     * Upload profile photo
     * 
     * Mengupload foto profil user yang sedang login.
     * Format yang didukung: jpeg, png, jpg. Maksimal 2MB.
     */
    public function uploadPhoto(UploadPhotoRequest $request): JsonResponse
    {
        $user = $request->user();

        // Delete old photo if exists
        if ($user->photo_url) {
            $oldPath = str_replace('/storage/', '', $user->photo_url);
            Storage::disk('public')->delete($oldPath);
        }

        // Store new photo with custom filename
        $file = $request->file('photo');
        $filename = 'profile_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('photos/profiles', $filename, 'public');
        $photoUrl = '/storage/' . $path;

        $user->update(['photo_url' => $photoUrl]);

        return response()->json([
            'success' => true,
            'message' => 'Foto profil berhasil diupload',
            'data' => [
                'photo_url' => $photoUrl,
            ],
        ], 200);
    }

    /**
     * Delete profile photo
     * 
     * Menghapus foto profil user yang sedang login.
     */
    public function deletePhoto(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->photo_url) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada foto profil untuk dihapus',
            ], 400);
        }

        // Delete photo from storage
        $oldPath = str_replace('/storage/', '', $user->photo_url);
        Storage::disk('public')->delete($oldPath);

        $user->update(['photo_url' => null]);

        return response()->json([
            'success' => true,
            'message' => 'Foto profil berhasil dihapus',
        ], 200);
    }

    /**
     * Get leaderboard
     * 
     * Mendapatkan daftar user dengan peringkat berdasarkan jumlah barang yang berhasil dikembalikan.
     * User dengan kontribusi terbanyak akan berada di posisi teratas.
     */
    public function leaderboard(Request $request): JsonResponse
    {
        $limit = $request->query('limit', 10);

        // Get users with their returned items count
        // A returned item counts if:
        // 1. User posted a FOUND item and it's returned
        // 2. User claimed a LOST item (found it), claim is approved, and item is returned
        
        $users = \App\Models\User::select('users.*')
            ->selectRaw("
                (
                    (SELECT COUNT(*) FROM items WHERE items.user_id = users.id AND items.type = 'found' AND items.status = 'returned') 
                    + 
                    (SELECT COUNT(*) FROM claims JOIN items ON claims.item_id = items.id WHERE claims.claimer_id = users.id AND items.type = 'lost' AND items.status = 'returned' AND claims.status = 'approved')
                ) as returned_count
            ")
            ->selectRaw("
                (
                    (SELECT COUNT(*) FROM items WHERE items.user_id = users.id AND items.type = 'found')
                    +
                    (SELECT COUNT(*) FROM claims JOIN items ON claims.item_id = items.id WHERE claims.claimer_id = users.id AND items.type = 'lost' AND claims.status = 'approved')
                ) as found_count
            ")
            ->get()
            ->filter(function ($user) {
                return $user->returned_count > 0 || $user->found_count > 0;
            })
            ->sortByDesc('found_count')
            ->sortByDesc('returned_count')
            ->take($limit)
            ->values();

        $leaderboard = $users->map(function ($user, $index) {
            return [
                'rank' => $index + 1,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'prodi_unit' => $user->prodi_unit,
                    'photo_url' => $user->photo_url,
                ],
                'stats' => [
                    'returned_count' => (int) $user->returned_count,
                    'found_count' => (int) $user->found_count,
                ],
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Leaderboard berhasil diambil',
            'data' => [
                'leaderboard' => $leaderboard,
            ],
        ], 200);
    }
}
