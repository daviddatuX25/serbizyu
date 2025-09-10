@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'status-message status-success']) }}>
        {{ $status }}
    </div>
@endif