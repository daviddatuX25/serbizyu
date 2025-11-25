<?php

namespace App\Domains\Admin\Http\Controllers;

use App\Domains\Listings\Models\Service;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ListingManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = Service::paginate(10);
        return view('admin.listings.index', compact('services'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        return view('admin.listings.show', compact('service'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        return view('admin.listings.edit', compact('service'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        // Add validation and update logic later
        return redirect()->route('admin.listings.index')->with('success', 'Listing updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->route('admin.listings.index')->with('success', 'Listing deleted successfully.');
    }
}
