<?php 
  @props(['openoffer']);
  $budget = $openoffer->budget ?? 'N/A';
  
?>

<!-- Open Offer Card -->
<article class="listing-card">
  <div class="card-top">
    <span class="badge-offer">Open Offer</span>
    <span class="text-sm text-text-secondary">Budget: {{$openoffer->budget}}</span>
  </div>
  <h3 class="card-title">Looking for Catering Service</h3>
  <p class="card-desc">Event for 50 guests</p>
  <p class="card-meta">Sta. Cruz, Ilocos Sur</p>
  <div class="card-footer">
    <span class="text-xs text-text-secondary">Posted 2h ago</span>
    <div class="card-avatar">📝</div>
  </div>
</article>