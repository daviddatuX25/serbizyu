<?php 
  /**
   * Open Offer Card Component
   * 
   * @props ['openoffer'] - OpenOffer model instance with relationships loaded
   * Expected relationships: creator, bids, address
   */
  
  // Format budget
  $formattedBudget = 'Budget not specified';
  if ($openoffer->budget_from && $openoffer->budget_to) {
      $formattedBudget = "₱" . number_format($openoffer->budget_from, 2) . " - ₱" . number_format($openoffer->budget_to, 2);
  } elseif ($openoffer->budget_from) {
      $formattedBudget = "From ₱" . number_format($openoffer->budget_from, 2);
  } elseif ($openoffer->budget_to) {
      $formattedBudget = "Up to ₱" . number_format($openoffer->budget_to, 2);
  }

  // Get location from address
  $location = $openoffer->address ? 
    ($openoffer->address->city ?? '') . ($openoffer->address->province ? ', ' . $openoffer->address->province : '') : 
    'Location not specified';
  
  // Get creator info
  $creatorName = $openoffer->creator->name ?? 'Unknown';
  $creatorInitial = strtoupper(substr($creatorName, 0, 1));
  
  // Get bid count
  $bidCount = $openoffer->bids->count();
?>

<article class="listing-card">
    <div class="card-top">
        <span class="badge-offer">Open Offer</span>
        <span class="text-sm text-text-secondary">{{ $bidCount }} {{ Str::plural('Bid', $bidCount) }}</span>
    </div>
    
    <h3 class="card-title">{{ $openoffer->title }}</h3>
    
    <p class="card-desc">{{ Str::limit($openoffer->description, 80) }}</p>
    
    <p class="card-meta">Budget: {{ $formattedBudget }}</p>
    
    <p class="card-meta">📍 {{ $location }}</p>
    
    <div class="card-footer">
        <span class="text-xs text-text-secondary">
            Posted {{ $openoffer->created_at->diffForHumans() }}
        </span>
        <div class="card-avatar" title="{{ $creatorName }}">
            {{ $creatorInitial }}
        </div>
    </div>

    <div class="card-actions">
        @can('update', $openoffer)
            <a href="{{ route('creator.openoffers.edit', $openoffer) }}" class="btn-primary">Manage</a>
        @else
            <a href="{{ route('openoffers.show', $openoffer) }}" class="btn-secondary">View Offer & Bid</a>
        @endcan
    </div>
</article>



