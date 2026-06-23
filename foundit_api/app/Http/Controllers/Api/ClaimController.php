<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\Item;
use Illuminate\Http\Request;

class ClaimController extends Controller
{
    /**
     * Get Claims for an Item
     *
     * Mendapatkan daftar klaim untuk item tertentu (hanya pemilik item yang bisa lihat).
     *
     * @urlParam item_id integer required ID dari item. Example: 1
     */
    public function index($itemId)
    {
        $item = Item::find($itemId);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Item tidak ditemukan',
            ], 404);
        }

        // Hanya pemilik item yang bisa lihat klaim
        if ($item->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses',
            ], 403);
        }

        $claims = $item->claims()
            ->with('claimer:id,name,email,prodi_unit,phone')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $claims,
        ]);
    }

    /**
     * Create Claim
     *
     * Membuat klaim untuk barang temuan.
     *
     * @bodyParam item_id integer required ID item yang diklaim. Example: 1
     * @bodyParam reason string required Alasan klaim (min 20 karakter). Example: Ini barang saya yang hilang minggu lalu
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'reason' => 'required|string|min:20',
        ]);

        $item = Item::find($request->item_id);

        // Validasi: Tidak bisa klaim barang sendiri
        if ($item->user_id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak bisa mengklaim barang sendiri',
            ], 422);
        }

        // Validasi: Item masih aktif
        if ($item->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Item sudah tidak aktif',
            ], 422);
        }

        // Cek apakah user sudah punya klaim pending atau approved
        $existingClaim = Claim::where('item_id', $request->item_id)
            ->where('claimer_id', auth()->id())
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existingClaim) {
            if ($existingClaim->status === 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Klaim Anda sudah disetujui sebelumnya',
                ], 422);
            }
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah memiliki klaim yang sedang menunggu review',
            ], 422);
        }

        // Buat klaim baru
        $claim = Claim::create([
            'item_id' => $request->item_id,
            'claimer_id' => auth()->id(),
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        $claim->load(['item', 'claimer:id,name,email,prodi_unit']);

        return response()->json([
            'success' => true,
            'message' => 'Klaim berhasil dikirim',
            'data' => $claim,
        ], 201);
    }

    /**
     * Get My Claims
     *
     * Mendapatkan daftar klaim yang saya buat.
     */
    public function myClaims()
    {
        $claims = Claim::where('claimer_id', auth()->id())
            ->with(['item' => function ($query) {
                $query->with(['category:id,name', 'photos', 'user:id,name,email,phone,prodi_unit,photo_url']);
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        // Add photo_url to each item
        $claims->transform(function ($claim) {
            if ($claim->item && $claim->item->photos->isNotEmpty()) {
                $claim->item->photo_url = url(\Illuminate\Support\Facades\Storage::url(
                    $claim->item->photos->first()->photo_url
                ));
            }
            return $claim;
        });

        return response()->json([
            'success' => true,
            'data' => $claims,
        ]);
    }

    /**
     * Approve Claim
     *
     * Menyetujui klaim barang (hanya pemilik item yang bisa approve).
     *
     * @urlParam id integer required ID dari klaim. Example: 1
     */
    public function approve($id)
    {
        $claim = Claim::with('item')->find($id);

        if (!$claim) {
            return response()->json([
                'success' => false,
                'message' => 'Klaim tidak ditemukan',
            ], 404);
        }

        // Hanya pemilik item yang bisa approve
        if ($claim->item->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses',
            ], 403);
        }

        // Validasi: Hanya klaim pending yang bisa di-approve
        if ($claim->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Klaim sudah diproses sebelumnya',
            ], 422);
        }

        // Generate unique verification code for pickup
        $verificationCode = strtoupper(\Illuminate\Support\Str::random(8));

        // Approve klaim
        $claim->update([
            'status' => 'approved',
            'reviewed_at' => now(),
            'verification_code' => $verificationCode,
        ]);

        // Update status item menjadi 'claimed'
        $claim->item->update(['status' => 'claimed']);

        // Reject semua klaim lainnya untuk item yang sama
        Claim::where('item_id', $claim->item_id)
            ->where('id', '!=', $claim->id)
            ->where('status', 'pending')
            ->update([
                'status' => 'rejected',
                'reviewed_at' => now(),
            ]);

        $claim->load('claimer:id,name,email,prodi_unit,phone');

        return response()->json([
            'success' => true,
            'message' => 'Klaim berhasil disetujui',
            'data' => $claim,
        ]);
    }

    /**
     * Reject Claim
     *
     * Menolak klaim barang (hanya pemilik item yang bisa reject).
     *
     * @urlParam id integer required ID dari klaim. Example: 1
     */
    public function reject(Request $request, $id)
    {
        $claim = Claim::with('item')->find($id);

        if (!$claim) {
            return response()->json([
                'success' => false,
                'message' => 'Klaim tidak ditemukan',
            ], 404);
        }

        // Hanya pemilik item yang bisa reject
        if ($claim->item->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses',
            ], 403);
        }

        // Validasi: Hanya klaim pending yang bisa di-reject
        if ($claim->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Klaim sudah diproses sebelumnya',
            ], 422);
        }

        $request->validate([
            'reason' => 'required|string|min:5',
        ]);

        // Reject klaim
        $claim->update([
            'status' => 'rejected',
            'rejection_reason' => $request->reason,
            'reviewed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Klaim berhasil ditolak',
            'data' => $claim,
        ]);
    }
}
