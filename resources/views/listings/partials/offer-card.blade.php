<?php 
  /**
   * Open Offer Card Component
   * 
   * @props ['offer'] - OpenOffer model instance with relationships loaded
   * Expected relationships: creator, bids, address
   */
  
  // Format budget
  $formattedBudget = 'Budget not specified';
  if ($offer->budget_from && $offer->budget_to) {
      $formattedBudget = "₱" . number_format($offer->budget_from, 2) . " - ₱" . number_format($offer->budget_to, 2);
  } elseif ($offer->budget_from) {
      $formattedBudget = "From ₱" . number_format($offer->budget_from, 2);
  } elseif ($offer->budget_to) {
      $formattedBudget = "Up to ₱" . number_format($offer->budget_to, 2);
  }

  // Get location from address
  $location = $offer->address ? 
    ($offer->address->city ?? '') . ($offer->address->province ? ', ' . $offer->address->province : '') : 
    'Location not specified';
  
  // Get creator info
  $creatorName = $offer->creator->name ?? 'Unknown';
  $creatorInitial = strtoupper(substr($creatorName, 0, 1));
  
  // Get bid count
  $bidCount = $offer->bids->count();
?>

<article class="listing-card">
    <div class="card-top">
        <span class="badge-offer">Open Offer</span>
        <span class="text-sm text-text-secondary">{{ $bidCount }} {{ Str::plural('Bid', $bidCount) }}</span>
    </div>
    
    <h3 class="card-title">{{ $offer->title }}</h3>
    
    <p class="card-desc">{{ Str::limit($offer->description, 80) }}</p>
    
    <p class="card-meta">Budget: {{ $formattedBudget }}</p>
    
    <p class="card-meta">📍 {{ $location }}</p>
    
    <div class="card-footer">
        <span class="text-xs text-text-secondary">
            Posted {{ $offer->created_at->diffForHumans() }}
        </span>
        <div class="card-avatar" title="{{ $creatorName }}">
            {{ $creatorInitial }}
        </div>
    </div>

    <div class="card-actions">
        @can('update', $offer)
            <a href="{{ route('creator.offers.edit', $offer) }}" class="btn-primary">Manage</a>
        @else
            <a href="{{ route('open-offers.show', $offer) }}" class="btn-secondary">View Offer & Bid</a>
        @endcan
    </div>
</article>