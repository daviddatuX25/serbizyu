<?php

namespace App\Console\Commands;

use App\Domains\Listings\Models\Service;
use Illuminate\Console\Command;

class UpdateServiceAverageRatings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:update-ratings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update and cache average ratings for all services based on their reviews';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating service average ratings...');

        $services = Service::all();
        $updated = 0;

        foreach ($services as $service) {
            $service->updateAverageRating();
            $updated++;

            if ($updated % 50 === 0) {
                $this->line("Updated $updated services...");
            }
        }

        $this->info("✓ Successfully updated average ratings for $updated services");
    }
}
