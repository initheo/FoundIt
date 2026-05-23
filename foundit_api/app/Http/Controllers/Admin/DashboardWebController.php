<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Item;
use App\Models\Claim;
use Illuminate\Http\Request;

class DashboardWebController extends Controller
{
    public function index()
    {
        $stats = [
            'total_items' => Item::count(),
            'active_items' => Item::where('status', 'active')->count(),
            'resolved_items' => Item::where('status', 'returned')->count(),
            'pending_claims' => Claim::where('status', 'pending')->count(),
            'total_users' => User::where('role', '!=', 'admin')->count(),
            
            // Recent activity lists
            'latest_claims' => Claim::with(['claimer', 'item'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(),
                
            'latest_items' => Item::with(['user', 'category'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(),
        ];

        $mapItems = Item::with(['photos', 'category'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'type' => $item->type,
                    'status' => $item->status,
                    'latitude' => (float) $item->latitude,
                    'longitude' => (float) $item->longitude,
                    'location' => $item->location,
                    'category_name' => $item->category->name ?? 'Tanpa Kategori',
                    'photo_url' => $item->photos->first() ? asset('storage/' . $item->photos->first()->photo_url) : null,
                    'detail_url' => route('admin.items.show', $item->id),
                ];
            });

        return view('admin.dashboard', compact('stats', 'mapItems'));
    }
}
