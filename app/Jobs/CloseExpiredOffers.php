<?php

namespace App\Jobs;

use App\Domains\Listings\Models\OpenOffer;
use App\Enums\OpenOfferStatus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Date;

class CloseExpiredOffers implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        OpenOffer::where('deadline', '<=', Date::now())
            ->where('status', '!=', OpenOfferStatus::CLOSED)
            ->update(['status' => OpenOfferStatus::EXPIRED]);
    }
}
