<?php 
  /**
   * Service Card Component
   * 
   * @props ['service'] - Service model instance with relationships loaded
   * Expected relationships: category, creator, workflow, address
   */
  
  // Format price with peso sign
  $formattedPrice = "₱" . number_format($service->price ?? 0, 2);
  
  // Get pay_first label
  $paymentType = $service->pay_first ? 'Pay First' : 'Pay After';
  
  // Get workflow steps (shortened for display) order them by workTemplate->order_index
    $workflowSteps = '';
    if ($service->workflowTemplate && $service->workflowTemplate->workTemplates) {
        $steps = $service->workflowTemplate->workTemplates
            ->sortBy('order_index')
            ->pluck('title')
            ->toArray();
        $workflowSteps = implode(' → ', $steps);
    }
  
  // Get location from address
  $location = $service->address ? 
    ($service->address->city ?? '') . ($service->address->province ? ', ' . $service->address->province : '') : 
    'Location not specified';
  
  // Get creator info
  $creatorName = $service->creator->name ?? 'Unknown';
  $creatorInitial = strtoupper(substr($creatorName, 0, 1)); // initials for now
  
  // Placeholder rating (you'll want to implement actual ratings later)
  $rating = '★★★★☆';
?>

<article class="listing-card">
    <div class="card-top">
        <span class="badge-service">Service</span>
        <span class="rating">{{ $rating }}</span>
    </div>
    
    <h3 class="card-title">{{ $service->title }}</h3>
    
    <p class="card-desc">{{ Str::limit($service->description, 80) }}</p>
    
    @if($service->workflow)
        <p class="card-desc text-xs italic">{{ $workflowSteps }}</p>
    @endif
    
    <p class="card-meta">Price: {{ $formattedPrice }}{{ $service->pay_first ? '/hr' : '' }}</p>
    
    <p class="card-meta">📍 {{ $location }}</p>
    
    @if($service->category)
        <p class="card-meta text-xs">
            <span class="text-gray-400">Category:</span> {{ $service->category->name }}
        </p>
    @endif
    
    <div class="card-footer">
        <span class="text-xs text-text-secondary">
            {{ $paymentType }} • {{ $service->created_at->diffForHumans() }}
        </span>
        <div class="card-avatar" title="{{ $creatorName }}">
            {{ $creatorInitial }}
        </div>
    </div>
</article>