<?php

namespace App\Domains\Admin\Http\Controllers;

use App\Domains\Listings\Models\OpenOffer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OpenOfferManagementController extends Controller
{
    /**
     * Display a listing of open offers
     */
    public function index(Request $request)
    {
        $query = OpenOffer::query()->with(['creator', 'category']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $openOffers = $query->paginate(15);

        return view('admin.openoffers.index', compact('openOffers'));
    }

    /**
     * Redirect to public-facing show page
     */
    public function show(OpenOffer $openOffer)
    {
        return redirect()->route('openoffers.show', ['openoffer' => $openOffer->id]);
    }
}
