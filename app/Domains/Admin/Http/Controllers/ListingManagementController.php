<?php

namespace App\Domains\Admin\Http\Controllers;

use App\Domains\Listings\Models\Service;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ListingManagementController extends Controller
{
    /**
     * Display a listing of services
     */
    public function index(Request $request)
    {
        $query = Service::query()->with(['creator', 'category']);

        // Search by title or description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by price range
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        // Sort by
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $services = $query->paginate(15);

        return view('admin.listings.index', compact('services'));
    }

    /**
     * Redirect to public-facing show page
     */
    public function show(Service $service)
    {
        return redirect("/services/{$service->id}");
    }
}
