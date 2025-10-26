<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class Listing extends Component
{

    // initialze with a listing model
    
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    public $listing;

    public $type;
    public $title;
    public $workflow; // array of steps (strings)
    public $workflowString; // joined string "Step1 > Step2"
    public $price;
    public $address;
    public $badge;
    public $avatar;
    public $rating; // float like 4.5

    /**
     * Create the component instance.
     *
     * @param  mixed  $listing  Eloquent model instance (Service | OpenOffer)
     * @return void
     */
    public function __construct($listing)
    {
        $this->listing = $listing;

        // Determine type by class name
        $base = class_basename($listing);
        $this->type = Str::lower($base); // service or openoffer (or openOffer -> openoffer)

        // Title
        $this->title = $listing->title ?? ($listing->name ?? 'Untitled');

        // Workflow extraction (array)
        $this->workflow = $this->extractWorkflow($listing);
        $this->workflowString = implode(' > ', $this->workflow);

        // Price / Budget
        if (property_exists($listing, 'price') || isset($listing->price)) {
            $this->price = $this->formatMoney($listing->price);
        } elseif (property_exists($listing, 'budget') || isset($listing->budget)) {
            $this->price = $this->formatMoney($listing->budget);
        } else {
            $this->price = null;
        }

        // Address (try to pull first address of creator)
        $this->address = $this->extractAddress($listing);

        // Badge: prefer a property 'badge' if provided, else try to detect verified user or created_at
        $this->badge = $this->extractBadge($listing);

        // Avatar placeholder (you may supply path via relation)
        $this->avatar = $this->extractAvatar($listing);

        // Rating: average of ListingReview ratings for this listing (if model exists)
        $this->rating = $this->extractRating($listing);
    }

    protected function extractWorkflow($listing)
    {
        // 1) If listing has relation to workflow_template -> workTemplates -> step title
        try {
            if (method_exists($listing, 'workflowTemplate') && $listing->workflowTemplate) {
                $steps = $listing->workflowTemplate->workTemplates()
                    ->orderBy('step_index')
                    ->pluck('title')
                    ->filter()
                    ->toArray();

                if (!empty($steps)) {
                    return array_values($steps);
                }
            }

            // 2) If listing has workflow_template relation nested differently
            if (isset($listing->workflow_template) && $listing->workflow_template) {
                // expecting ->workTemplates
                if (isset($listing->workflow_template->workTemplates)) {
                    $steps = collect($listing->workflow_template->workTemplates)
                        ->sortBy('step_index')
                        ->pluck('title')
                        ->filter()
                        ->toArray();

                    if (!empty($steps)) return array_values($steps);
                }
            }

            // 3) If listing has 'workflow' attribute stored as json/array
            if (isset($listing->workflow) && !empty($listing->workflow)) {
                if (is_string($listing->workflow)) {
                    $decoded = @json_decode($listing->workflow, true);
                    if (is_array($decoded)) {
                        return array_values(array_filter($decoded));
                    }
                    // fallback: split by '>' or newline
                    $parts = preg_split('/\s*>\s*|\r\n|\n/', $listing->workflow);
                    return array_values(array_filter(array_map('trim', $parts)));
                } elseif (is_array($listing->workflow)) {
                    return array_values(array_filter($listing->workflow));
                }
            }

            // 4) As last resort, try to parse description for '>' separators
            if (isset($listing->description) && is_string($listing->description)) {
                if (str_contains($listing->description, '>')) {
                    $parts = preg_split('/\s*>\s*/', $listing->description);
                    return array_values(array_filter(array_map('trim', $parts)));
                }
            }
        } catch (\Throwable $e) {
            // ignore and fallback
        }

        // Default empty
        return [];
    }

    protected function extractAddress($listing)
    {
        try {
            if (isset($listing->creator)) {
                $creator = $listing->creator;

                // If user has addresses relationship
                if (method_exists($creator, 'addresses')) {
                    $addr = $creator->addresses()->first();
                    if ($addr) {
                        // Compose friendly address; adjust fields depending on your Address model
                        $parts = array_filter([
                            $addr->house_no ?? null,
                            $addr->street ?? null,
                            $addr->barangay ?? null,
                            $addr->town ?? null,
                            $addr->province ?? null,
                        ]);
                        return implode(', ', $parts);
                    }
                }

                // If the creator has town / province columns directly
                if (isset($creator->town) || isset($creator->province)) {
                    $parts = array_filter([
                        $creator->town ?? null,
                        $creator->province ?? null,
                    ]);
                    return implode(', ', $parts);
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return null;
    }

    protected function extractBadge($listing)
    {
        // If listing itself has a badge attribute
        if (isset($listing->badge) && $listing->badge) {
            return $listing->badge;
        }

        // Try user verification flag (common names: is_verified, verified, verified_at)
        try {
            if (isset($listing->creator)) {
                $u = $listing->creator;
                if (isset($u->is_verified) && $u->is_verified) {
                    return 'Verified Servicer';
                }
                if (isset($u->verified) && $u->verified) {
                    return 'Verified';
                }
                if (isset($listing->created_at)) {
                    // If recent, say "Posted X ago" (simple fallback)
                    return 'Posted ' . $listing->created_at->diffForHumans();
                }
            }
        } catch (\Throwable $e) {
            //
        }

        return null;
    }

    protected function extractAvatar($listing)
    {
        try {
            if (isset($listing->creator) && isset($listing->creator->avatar_url)) {
                return $listing->creator->avatar_url;
            }
            // fallback: gravatar or placeholder - return null and the blade will show emoji
            return null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function extractRating($listing)
    {
        try {
            // If you have a ListingReview model with fields: listing_type, listing_id, rating
            if (class_exists(ListingReview::class)) {
                $listingType = class_basename($listing); // e.g. Service
                $avg = ListingReview::where('listing_type', $listingType)
                    ->where('listing_id', $listing->id)
                    ->avg('rating');

                if ($avg === null) return null;

                // Round to nearest 0.5
                return round($avg * 2) / 2;
            }
        } catch (\Throwable $e) {
            //
        }

        return null;
    }

    protected function formatMoney($value)
    {
        if ($value === null) return null;
        return '₱' . number_format((float)$value, 0); // no decimals by default; adjust if needed
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.listing-card');
    }
    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        // determine which view to load based on the type of listing


    }
}

