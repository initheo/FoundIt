<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @tags Activity
 */
class ActivityController extends Controller
{
    /**
     * Get user activity history
     * 
     * Mendapatkan riwayat aktivitas user yang sedang login.
     * Aktivitas meliputi: laporan barang, klaim yang dibuat, klaim yang diterima, dll.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $limit = $request->query('limit', 20);

        $activities = collect();

        // 1. Items reported by user
        $reportedItems = Item::where('user_id', $user->id)
            ->with(['category', 'photos'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => 'item_' . $item->id,
                    'type' => 'item_reported',
                    'title' => $item->type === 'lost' 
                        ? 'Melaporkan barang hilang' 
                        : 'Melaporkan barang temuan',
                    'description' => $item->title,
                    'icon' => $item->type === 'lost' ? 'search' : 'inventory_2',
                    'color' => $item->type === 'lost' ? 'warning' : 'success',
                    'reference_id' => $item->id,
                    'reference_type' => 'item',
                    'photo_url' => $item->photos->first() 
                        ? '/storage/' . $item->photos->first()->photo_url 
                        : null,
                    'created_at' => $item->created_at->format('Y-m-d H:i:s'),
                    'timestamp' => $item->created_at->timestamp,
                ];
            });

        // 2. Claims made by user
        $claimsMade = Claim::where('claimer_id', $user->id)
            ->with(['item', 'item.photos'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($claim) {
                $statusText = match($claim->status) {
                    'approved' => 'Klaim disetujui',
                    'rejected' => 'Klaim ditolak',
                    default => 'Mengajukan klaim',
                };
                $color = match($claim->status) {
                    'approved' => 'success',
                    'rejected' => 'error',
                    default => 'primary',
                };
                return [
                    'id' => 'claim_made_' . $claim->id,
                    'type' => 'claim_made',
                    'title' => $statusText,
                    'description' => $claim->item?->title ?? 'Item tidak ditemukan',
                    'icon' => 'how_to_reg',
                    'color' => $color,
                    'reference_id' => $claim->id,
                    'reference_type' => 'claim',
                    'photo_url' => $claim->item?->photos->first() 
                        ? '/storage/' . $claim->item->photos->first()->photo_url 
                        : null,
                    'created_at' => $claim->created_at->format('Y-m-d H:i:s'),
                    'timestamp' => $claim->created_at->timestamp,
                ];
            });

        // 3. Claims received on user's items
        $claimsReceived = Claim::whereHas('item', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with(['item', 'item.photos', 'claimer'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($claim) {
                return [
                    'id' => 'claim_received_' . $claim->id,
                    'type' => 'claim_received',
                    'title' => 'Menerima klaim baru',
                    'description' => ($claim->claimer?->name ?? 'Seseorang') . ' mengklaim ' . ($claim->item?->title ?? 'item'),
                    'icon' => 'person_add',
                    'color' => 'info',
                    'reference_id' => $claim->id,
                    'reference_type' => 'claim',
                    'photo_url' => $claim->item?->photos->first() 
                        ? '/storage/' . $claim->item->photos->first()->photo_url 
                        : null,
                    'created_at' => $claim->created_at->format('Y-m-d H:i:s'),
                    'timestamp' => $claim->created_at->timestamp,
                ];
            });

        // 4. Items returned (status changed to 'returned')
        $returnedItems = Item::where('user_id', $user->id)
            ->where('status', 'returned')
            ->with(['category', 'photos'])
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => 'item_returned_' . $item->id,
                    'type' => 'item_returned',
                    'title' => 'Barang dikembalikan',
                    'description' => $item->title,
                    'icon' => 'check_circle',
                    'color' => 'success',
                    'reference_id' => $item->id,
                    'reference_type' => 'item',
                    'photo_url' => $item->photos->first() 
                        ? '/storage/' . $item->photos->first()->photo_url 
                        : null,
                    'created_at' => $item->updated_at->format('Y-m-d H:i:s'),
                    'timestamp' => $item->updated_at->timestamp,
                ];
            });

        // Merge and sort by timestamp
        $activities = $reportedItems
            ->concat($claimsMade)
            ->concat($claimsReceived)
            ->concat($returnedItems)
            ->sortByDesc('timestamp')
            ->take($limit)
            ->values();

        return response()->json([
            'success' => true,
            'message' => 'Riwayat aktivitas berhasil diambil',
            'data' => [
                'activities' => $activities,
            ],
        ], 200);
    }
}