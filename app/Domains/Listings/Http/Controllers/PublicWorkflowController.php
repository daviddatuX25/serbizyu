<?php

namespace App\Domains\Listings\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use App\Domains\Listings\Models\WorkflowTemplate;
use App\Domains\Listings\Services\WorkflowBookmarkService;
use App\Domains\Users\Models\User;

class PublicWorkflowController extends Controller
{
    /**
     * Display a listing of public workflow templates that can be bookmarked.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('creator.workflows.public.index');
    }

    /**
     * Bookmark a public workflow template for the authenticated user.
     *
     * @param WorkflowTemplate $workflowTemplate
     * @param WorkflowBookmarkService $bookmarkService
     * @return RedirectResponse|JsonResponse
     */
    public function bookmark(WorkflowTemplate $workflowTemplate, WorkflowBookmarkService $bookmarkService): RedirectResponse|JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        // Authorization: Ensure the workflow is public and not owned by the current user
        if (!$workflowTemplate->is_public || $workflowTemplate->creator_id === $user->id) {
            return request()->expectsJson()
                ? response()->json(['message' => 'Cannot bookmark this workflow.'], 403)
                : back()->with('error', 'Cannot bookmark this workflow.');
        }

        $bookmarked = $bookmarkService->bookmarkWorkflow($user, $workflowTemplate);

        if (request()->expectsJson()) {
            return $bookmarked
                ? response()->json(['message' => 'Workflow bookmarked successfully.'])
                : response()->json(['message' => 'Workflow already bookmarked.'], 200);
        }

        return $bookmarked
            ? back()->with('success', 'Workflow bookmarked successfully.')
            : back()->with('info', 'Workflow already bookmarked.');
    }

    /**
     * Unbookmark a public workflow template for the authenticated user.
     *
     * @param WorkflowTemplate $workflowTemplate
     * @param WorkflowBookmarkService $bookmarkService
     * @return RedirectResponse|JsonResponse
     */
    public function unbookmark(WorkflowTemplate $workflowTemplate, WorkflowBookmarkService $bookmarkService): RedirectResponse|JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $unbookmarked = $bookmarkService->unbookmarkWorkflow($user, $workflowTemplate);

        if (request()->expectsJson()) {
            return $unbookmarked
                ? response()->json(['message' => 'Workflow unbookmarked successfully.'])
                : response()->json(['message' => 'Workflow was not bookmarked.'], 200);
        }

        return $unbookmarked
            ? back()->with('success', 'Workflow unbookmarked successfully.')
            : back()->with('info', 'Workflow was not bookmarked.');
    }
}
