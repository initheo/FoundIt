<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\Item;
use Illuminate\Http\Request;

class ClaimWebController extends Controller
{
    public function index(Request $request)
    {
        $query = Claim::with(['claimer', 'item']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by claimer name, email, or item title
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('claimer', function ($q2) use ($search) {
                    $q2->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%'])
                       ->orWhereRaw('LOWER(email) LIKE ?', ['%' . strtolower($search) . '%']);
                })->orWhereHas('item', function ($q2) use ($search) {
                    $q2->whereRaw('LOWER(title) LIKE ?', ['%' . strtolower($search) . '%']);
                });
            });
        }

        $claims = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.claims.index', compact('claims'));
    }

    public function show($id)
    {
        $claim = Claim::with(['claimer', 'item.photos', 'item.category', 'item.user'])->findOrFail($id);
        
        // Find other claims for this item
        $otherClaims = Claim::with('claimer')
            ->where('item_id', $claim->item_id)
            ->where('id', '!=', $claim->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.claims.show', compact('claim', 'otherClaims'));
    }

    public function approve(Request $request, $id)
    {
        $claim = Claim::with('item')->findOrFail($id);

        if ($claim->status !== 'pending') {
            return redirect()->back()->with('error', 'Klaim ini sudah diproses sebelumnya.');
        }

        // Approve this claim
        $claim->update([
            'status' => 'approved',
            'reviewed_at' => now(),
        ]);

        // Update item status to claimed
        $claim->item->update(['status' => 'claimed']);

        // Reject other pending claims for this item
        Claim::where('item_id', $claim->item_id)
            ->where('id', '!=', $claim->id)
            ->where('status', 'pending')
            ->update([
                'status' => 'rejected',
                'rejection_reason' => 'Klaim lain untuk barang ini telah disetujui.',
                'reviewed_at' => now(),
            ]);

        return redirect()->route('admin.claims.show', $claim->id)
            ->with('success', 'Klaim berhasil disetujui.');
    }

    public function reject(Request $request, $id)
    {
        $claim = Claim::findOrFail($id);

        if ($claim->status !== 'pending') {
            return redirect()->back()->with('error', 'Klaim ini sudah diproses sebelumnya.');
        }

        $request->validate([
            'reason' => 'required|string|min:5|max:1000',
        ]);

        $claim->update([
            'status' => 'rejected',
            'rejection_reason' => $request->reason,
            'reviewed_at' => now(),
        ]);

        return redirect()->route('admin.claims.show', $claim->id)
            ->with('success', 'Klaim berhasil ditolak.');
    }
}
