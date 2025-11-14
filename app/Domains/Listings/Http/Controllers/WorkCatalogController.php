<?php

namespace App\Domains\Listings\Http\Controllers;

use App\Domains\Listings\Services\WorkCatalogService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WorkCatalogController extends Controller
{
    public function __construct(private readonly WorkCatalogService $workCatalogService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $workCatalogs = $this->workCatalogService->getAllWorkCatalogs();

        return response()->json($workCatalogs);
    }
}
