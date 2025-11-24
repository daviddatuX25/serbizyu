<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkInstanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'workflow_template_id' => $this->workflow_template_id,
            'status' => $this->status,
            'current_step_index' => $this->current_step_index,
            'progress_percentage' => $this->getProgressPercentage(),
            'is_completed' => $this->isCompleted(),
            'has_started' => $this->hasStarted(),
            'started_at' => $this->started_at?->toIso8601String(),
            'completed_at' => $this->completed_at?->toIso8601String(),
            
            // Relationships
            'order' => new OrderResource($this->whenLoaded('order')),
            'workflow_template' => $this->whenLoaded('workflowTemplate', function () {
                return [
                    'id' => $this->workflowTemplate->id,
                    'name' => $this->workflowTemplate->name,
                    'description' => $this->workflowTemplate->description,
                ];
            }),
            
            // Steps information
            'steps' => $this->whenLoaded('workInstanceSteps', function () {
                return $this->workInstanceSteps->map(function ($step) {
                    return [
                        'id' => $step->id,
                        'step_index' => $step->step_index,
                        'name' => $step->workTemplate?->name ?? 'Step ' . ($step->step_index + 1),
                        'description' => $step->workTemplate?->description,
                        'status' => $step->status,
                        'is_current' => $step->isCurrent(),
                        'is_completed' => $step->isCompleted(),
                        'is_in_progress' => $step->isInProgress(),
                        'duration_minutes' => $step->getDurationMinutes(),
                        'started_at' => $step->started_at?->toIso8601String(),
                        'completed_at' => $step->completed_at?->toIso8601String(),
                        'activity_thread' => $step->activityThread ? [
                            'id' => $step->activityThread->id,
                            'title' => $step->activityThread->title,
                            'description' => $step->activityThread->description,
                            'message_count' => $step->activityThread->getMessageCount(),
                            'latest_message' => $step->activityThread->getLatestMessage() ? [
                                'id' => $step->activityThread->getLatestMessage()->id,
                                'content' => $step->activityThread->getLatestMessage()->content,
                                'user_name' => $step->activityThread->getLatestMessage()->user->name,
                                'created_at' => $step->activityThread->getLatestMessage()->created_at?->toIso8601String(),
                            ] : null,
                        ] : null,
                    ];
                });
            }),

            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
