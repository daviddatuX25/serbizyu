<?php

namespace App\Domains\Listings\Http\Controllers;

use App\Domains\Listings\Models\OpenOffer;
use App\Domains\Listings\Services\OpenOfferService;
use App\Domains\Listings\Http\Requests\StoreOpenOfferRequest;
use App\Domains\Listings\Http\Requests\UpdateOpenOfferRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OpenOfferController extends Controller
{
    protected $openOfferService;

    public function __construct(OpenOfferService $openOfferService)
    {
        $this->openOfferService = $openOfferService;
        $this->authorizeResource(OpenOffer::class, 'offer', ['except' => ['index', 'show']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $openOffers = OpenOffer::latest()->paginate(10); // Example: paginate offers
        return view('offers.index', compact('openOffers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('offers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOpenOfferRequest $request)
    {
        $openOffer = $this->openOfferService->createOpenOffer(
            Auth::user(),
            $request->validated(),
            $request->file('images') ?? []
        );

        return redirect()->route('creator.offers.show', $openOffer)
            ->with('success', 'Open Offer created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(OpenOffer $offer)
    {
        return view('offers.show', ['openOffer' => $offer]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OpenOffer $offer)
    {
        return view('offers.edit', ['openOffer' => $offer]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOpenOfferRequest $request, OpenOffer $offer)
    {
        $openOffer = $this->openOfferService->updateOpenOffer(
            $offer,
            $request->validated(),
            $request->file('images') ?? []
        );

        return redirect()->route('creator.offers.show', $openOffer)
            ->with('success', 'Open Offer updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OpenOffer $offer)
    {
        $this->openOfferService->deleteOpenOffer($offer);

        return redirect()->route('creator.offers.index')
            ->with('success', 'Open Offer deleted successfully!');
    }

    /**
     * Close the specified open offer.
     */
    public function close(OpenOffer $offer)
    {
        $this->authorize('close', $offer);

        $this->openOfferService->closeOpenOffer($offer);

        return back()->with('success', 'Open Offer closed successfully!');
    }
}
