<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domains\Listings\Models\WorkflowTemplate;
use App\Domains\Listings\Models\WorkTemplate;
use App\Domains\Listings\Models\WorkCatalog;
use App\Domains\Users\Models\User;

class WorkflowAndWorkTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        if ($users->isEmpty()) {
            $users = User::factory(5)->create();
        }
        
        $workCatalogs = WorkCatalog::all();
        if ($workCatalogs->isEmpty()) {
            $this->call(WorkCatalogSeeder::class);
            $workCatalogs = WorkCatalog::all();
        }

        // Plumbing Workflow
        $plumbingWorkflow = WorkflowTemplate::create([
            'name' => 'Basic Plumbing Service',
            'description' => 'Standard workflow for plumbing repairs and installations',
            'creator_id' => $users->random()->id,
            'is_public' => true,
        ]);

        $this->createWorkTemplates($plumbingWorkflow, $workCatalogs, [
            'Initial Consultation',
            'Site Inspection',
            'Material Purchase',
            'On-site Work',
            'Quality Check',
            'Final Cleanup',
            'Payment Collection',
        ]);

        // House Painting Workflow
        $paintingWorkflow = WorkflowTemplate::create([
            'name' => 'House Painting Service',
            'description' => 'Complete house painting workflow from prep to finish',
            'creator_id' => $users->random()->id,
            'is_public' => true,
        ]);

        $this->createWorkTemplates($paintingWorkflow, $workCatalogs, [
            'Initial Consultation',
            'Site Inspection',
            'Material Purchase',
            'On-site Work',
            'Quality Check',
            'Final Cleanup',
            'Client Walkthrough',
            'Payment Collection',
            'Documentation',
        ]);

        // Catering Workflow
        $cateringWorkflow = WorkflowTemplate::create([
            'name' => 'Event Catering Service',
            'description' => 'Full-service catering workflow for events',
            'creator_id' => $users->random()->id,
            'is_public' => true,
        ]);

        $this->createWorkTemplates($cateringWorkflow, $workCatalogs, [
            'Initial Consultation',
            'Material Purchase',
            'On-site Work',
            'Final Cleanup',
            'Client Walkthrough',
            'Payment Collection',
        ]);

        // Construction Workflow
        $constructionWorkflow = WorkflowTemplate::create([
            'name' => 'Small Construction Project',
            'description' => 'Workflow for minor construction and renovation work',
            'creator_id' => $users->random()->id,
            'is_public' => true,
        ]);

        $this->createWorkTemplates($constructionWorkflow, $workCatalogs, [
            'Initial Consultation',
            'Site Inspection',
            'Material Purchase',
            'On-site Work',
            'Quality Check',
            'Final Cleanup',
            'Client Walkthrough',
            'Payment Collection',
            'Documentation',
            'Follow-up Visit',
        ]);

        // Electrical Repair Workflow
        $electricalWorkflow = WorkflowTemplate::create([
            'name' => 'Electrical Repair Service',
            'description' => 'Standard workflow for electrical repairs and installations',
            'creator_id' => $users->random()->id,
            'is_public' => true,
        ]);

        $this->createWorkTemplates($electricalWorkflow, $workCatalogs, [
            'Initial Consultation',
            'Site Inspection',
            'Material Purchase',
            'On-site Work',
            'Quality Check',
            'Payment Collection',
            'Follow-up Visit',
        ]);

        // Event Decoration Workflow
        $decorationWorkflow = WorkflowTemplate::create([
            'name' => 'Event Decoration Service',
            'description' => 'Complete event decoration setup workflow',
            'creator_id' => $users->random()->id,
            'is_public' => true,
        ]);

        $this->createWorkTemplates($decorationWorkflow, $workCatalogs, [
            'Initial Consultation',
            'Site Inspection',
            'Material Purchase',
            'On-site Work',
            'Client Walkthrough',
            'Final Cleanup',
            'Payment Collection',
            'Documentation',
        ]);

        // Create some private workflows
        $privateWorkflow = WorkflowTemplate::create([
            'name' => 'Custom Client Workflow',
            'description' => 'Private workflow for specific client requirements',
            'creator_id' => $users->random()->id,
            'is_public' => false,
        ]);

        $this->createWorkTemplates($privateWorkflow, $workCatalogs, [
            'Initial Consultation',
            'On-site Work',
            'Payment Collection',
        ]);
    }

    private function createWorkTemplates($workflow, $workCatalogs, $catalogNames)
    {
        foreach ($catalogNames as $index => $catalogName) {
            $catalog = $workCatalogs->firstWhere('name', $catalogName);
            
            if ($catalog) {
                WorkTemplate::create([
                    'workflow_template_id' => $workflow->id,
                    'work_catalog_id' => $catalog->id,
                    'name' => $catalog->name,
                    'description' => $catalog->description,
                    'order' => $index,
                ]);
            }
        }
    }
}