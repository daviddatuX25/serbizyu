<?php

namespace App\Domains\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $activities = Activity::latest()->paginate(20);

        return view('admin.activity-logs.index', compact('activities'));
    }
}
