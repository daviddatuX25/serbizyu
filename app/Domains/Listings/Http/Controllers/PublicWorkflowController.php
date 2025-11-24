<?php

namespace App\Domains\Listings\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use App\Domains\Listings\Models\WorkflowTemplate;
use App\Domains\Listings\Services\WorkflowBookmarkService;
use App\Domains\Listings\Services\CategoryService;
use App\Domains\Users\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class PublicWorkflowController extends Controller
{
    /**
     * Display a listing of public workflow templates that can be bookmarked.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request, CategoryService $categoryService)
    {
        $filters = $request->all();
        $perPage = 12;
        $currentPage = $request->input('page', 1);

        $workflowsQuery = WorkflowTemplate::where('is_public', true)
            ->with(['category', 'creator']);

        if (isset($filters['search'])) {
            $workflowsQuery->where(function ($query) use ($filters) {
                $query->where('name', 'like', '%' . $filters['search'] . '%')
                      ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['category'])) {
            $workflowsQuery->where('category_id', $filters['category']);
        }

        $workflows = $workflowsQuery->orderBy('name')->get();
        
        $paginatedWorkflows = new LengthAwarePaginator(
            $workflows->forPage($currentPage, $perPage),
            $workflows->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('workflows.public.index', [
            'workflows' => $paginatedWorkflows,
            'categories' => $categoryService->listAllCategories(),
        ]);
    }

    /**
     * Bookmark a public workflow template for the authenticated user.
     *
     * @param WorkflowTemplate $workflowTemplate
     * @param WorkflowBookmarkService $bookmarkService
     * @return RedirectResponse|JsonResponse
     */
    public function bookmark(WorkflowTemplate $workflowTemplate, WorkflowBookmarkService $bookmarkService): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        // Authorization: Ensure the workflow is public and not owned by the current user
        if (!$workflowTemplate->is_public || $workflowTemplate->creator_id === $user->id) {
            return back()->with('error', 'Cannot bookmark this workflow.');
        }

        $bookmarked = $bookmarkService->bookmarkWorkflow($user, $workflowTemplate);

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
    public function unbookmark(WorkflowTemplate $workflowTemplate, WorkflowBookmarkService $bookmarkService): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $unbookmarked = $bookmarkService->unbookmarkWorkflow($user, $workflowTemplate);

        return $unbookmarked
            ? back()->with('success', 'Workflow unbookmarked successfully.')
            : back()->with('info', 'Workflow was not bookmarked.');
    }
}
