<?php

namespace Database\Seeders;

use App\Domains\Listings\Models\WorkflowTemplate;
use App\Domains\Users\Models\User;
use App\Domains\Users\Models\UserBookmarkedWorkflowTemplate;
use Illuminate\Database\Seeder;

class WorkflowBookmarkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $workflows = WorkflowTemplate::all();

        if ($users->isEmpty() || $workflows->isEmpty()) {
            $this->command->info('No users or workflows available to bookmark');

            return;
        }

        // Each user bookmarks a few random workflows
        $users->each(function (User $user) use ($workflows) {
            // Get 2-4 random workflows
            $count = rand(2, min(4, $workflows->count()));
            $randomWorkflows = $workflows->random($count);

            foreach ($randomWorkflows as $workflow) {
                UserBookmarkedWorkflowTemplate::firstOrCreate([
                    'user_id' => $user->id,
                    'workflow_template_id' => $workflow->id,
                ]);
            }
        });

        $this->command->info('Workflow bookmarks seeded successfully!');
    }
}
