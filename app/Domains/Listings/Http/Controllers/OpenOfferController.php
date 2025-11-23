<?php

namespace App\Domains\Listings\Http\Controllers;

use App\Domains\Listings\Models\OpenOffer;
use App\Domains\Listings\Services\OpenOfferService;
use App\Domains\Common\Services\AddressService;
use App\Domains\Listings\Http\Requests\StoreOpenOfferRequest;
use App\Domains\Listings\Http\Requests\UpdateOpenOfferRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OpenOfferController extends Controller
{
    protected $openOfferService;

    public function __construct(
        OpenOfferService $openOfferService,
        private AddressService $addressService
    ) {
        $this->openOfferService = $openOfferService;
        $this->authorizeResource(OpenOffer::class, 'openoffer', ['except' => ['index', 'show']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $offers = Auth::user()->openOffers()->latest()->paginate(10);
        return view('creator.offers.index', compact('offers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $addresses = $this->addressService->getAddressesForUser();
        return view('creator.offers.create', compact('addresses'));
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

        return redirect()->route('creator.openoffers.show', $openOffer)
            ->with('success', 'Open Offer created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(OpenOffer $openoffer)
    {
        $openoffer->load(['address', 'media']);
        $bids = $openoffer->bids()->with('bidder')->latest()->paginate(10);
        $userServices = Auth::check() ? Auth::user()->services : collect();

        return view('offers.show', [
            'offer' => $openoffer,
            'bids' => $bids,
            'userServices' => $userServices
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OpenOffer $openoffer)
    {
        $openoffer->load(['address', 'media']);
        $addresses = $this->addressService->getAddressesForUser();
        return view('creator.offers.edit', ['offer' => $openoffer, 'addresses' => $addresses]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOpenOfferRequest $request, OpenOffer $openoffer)
    {
        $openOffer = $this->openOfferService->updateOpenOffer(
            $openoffer,
            $request->validated(),
            $request->file('images') ?? []
        );

        return redirect()->route('creator.openoffers.show', $openOffer)
            ->with('success', 'Open Offer updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OpenOffer $openoffer)
    {
        $this->openOfferService->deleteOpenOffer($openoffer);

        return redirect()->route('creator.openoffers.index')
            ->with('success', 'Open Offer deleted successfully!');
    }

    /**
     * Close the specified open offer.
     */
    public function close(OpenOffer $openoffer)
    {
        $this->authorize('close', $openoffer);

        $this->openOfferService->closeOpenOffer($openoffer);

        return back()->with('success', 'Open Offer closed successfully!');
    }

    /**
     * Renew the specified open offer.
     */
    public function renew(OpenOffer $openoffer)
    {
        $this->authorize('renew', $openoffer);

        $this->openOfferService->renewOpenOffer($openoffer);

        return back()->with('success', 'Open Offer renewed successfully!');
    }
}
