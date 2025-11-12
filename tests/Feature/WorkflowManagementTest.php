<?php

namespace Tests\Feature;

use App\Domains\Listings\Models\WorkCatalog;
use App\Domains\Listings\Models\WorkflowTemplate;
use App\Domains\Listings\Models\WorkTemplate;
use App\Domains\Users\Models\User;
use App\Livewire\WorkflowBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class WorkflowManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_component_can_render()
    {
        $user = User::factory()->create();
        $workflow = WorkflowTemplate::factory()->create(['creator_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(WorkflowBuilder::class, ['workflow' => $workflow])
            ->assertStatus(200);
    }

    public function test_it_displays_existing_steps()
    {
        $user = User::factory()->create();
        $workflow = WorkflowTemplate::factory()->create(['creator_id' => $user->id]);
        WorkTemplate::factory()->create(['workflow_template_id' => $workflow->id, 'name' => 'First Step']);

        Livewire::actingAs($user)
            ->test(WorkflowBuilder::class, ['workflow' => $workflow])
            ->assertSee('First Step');
    }

    public function test_can_save_workflow_details()
    {
        $user = User::factory()->create();
        $workflow = WorkflowTemplate::factory()->create(['creator_id' => $user->id, 'name' => 'Old Name']);

        Livewire::actingAs($user)
            ->test(WorkflowBuilder::class, ['workflow' => $workflow])
            ->set('name', 'New Name')
            ->call('save');

        $this->assertDatabaseHas('workflow_templates', ['id' => $workflow->id, 'name' => 'New Name']);
    }

    public function test_can_add_a_new_step()
    {
        $user = User::factory()->create();
        $workflow = WorkflowTemplate::factory()->create(['creator_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(WorkflowBuilder::class, ['workflow' => $workflow])
            ->call('addStep');

        $this->assertDatabaseHas('work_templates', [
            'workflow_template_id' => $workflow->id,
            'name' => 'New Step',
        ]);
    }

    public function test_can_delete_a_step()
    {
        $user = User::factory()->create();
        $workflow = WorkflowTemplate::factory()->create(['creator_id' => $user->id]);
        $step = WorkTemplate::factory()->create(['workflow_template_id' => $workflow->id]);

        Livewire::actingAs($user)
            ->test(WorkflowBuilder::class, ['workflow' => $workflow])
            ->call('deleteStep', $step->id);

        $this->assertDatabaseMissing('work_templates', ['id' => $step->id]);
    }

    public function test_can_add_step_from_catalog()
    {
        $user = User::factory()->create();
        $workflow = WorkflowTemplate::factory()->create(['creator_id' => $user->id]);
        $catalogItem = WorkCatalog::factory()->create();

        Livewire::actingAs($user)
            ->test(WorkflowBuilder::class, ['workflow' => $workflow])
            ->call('addStepFromCatalog', $catalogItem->id);

        $this->assertDatabaseHas('work_templates', [
            'workflow_template_id' => $workflow->id,
            'work_catalog_id' => $catalogItem->id,
        ]);
    }

    public function test_can_reorder_steps()
    {
        $user = User::factory()->create();
        $workflow = WorkflowTemplate::factory()->create(['creator_id' => $user->id]);
        $step1 = WorkTemplate::factory()->create(['workflow_template_id' => $workflow->id, 'order' => 0]);
        $step2 = WorkTemplate::factory()->create(['workflow_template_id' => $workflow->id, 'order' => 1]);

        $newOrder = [
            ['value' => $step2->id, 'order' => 1],
            ['value' => $step1->id, 'order' => 2],
        ];

        Livewire::actingAs($user)
            ->test(WorkflowBuilder::class, ['workflow' => $workflow])
            ->call('updateStepOrder', $newOrder);

        $this->assertDatabaseHas('work_templates', ['id' => $step1->id, 'order' => 2]);
        $this->assertDatabaseHas('work_templates', ['id' => $step2->id, 'order' => 1]);
    }
    
    public function test_can_update_a_step()
    {
        $user = User::factory()->create();
        $workflow = WorkflowTemplate::factory()->create(['creator_id' => $user->id]);
        $step = WorkTemplate::factory()->create(['workflow_template_id' => $workflow->id, 'name' => 'Old Step Name']);

        Livewire::actingAs($user)
            ->test(WorkflowBuilder::class, ['workflow' => $workflow])
            ->call('editStep', $step->id)
            ->set('editingStep.name', 'New Step Name')
            ->call('updateStep');

        $this->assertDatabaseHas('work_templates', ['id' => $step->id, 'name' => 'New Step Name']);
    }
}
