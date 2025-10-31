<?php

namespace App\Domains\Listings\Http\Controllers;

use App\Domains\Listings\Models\OpenOffer;
use App\Domains\Listings\Models\Service;
use App\Domains\Listings\Services\ServiceService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ListingController extends Controller
{

    public function __construct()
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = app(ServiceService::class)->getAllServices();
        $openOffers = app(OpenOffer::class)->getOpenOffers();
        // concatenate models
        $listings = $services->concat($openOffers);
        return view('browse', compact('listings'));
    }


}
