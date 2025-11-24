// resources/js/app.js
import './bootstrap';

// Import Alpine core and plugins
import Alpine from 'alpinejs'
import focus from '@alpinejs/focus'
import collapse from '@alpinejs/collapse' // Import the collapse plugin

// Import Livewire 4 sortable (replaces old livewire-sortablejs)
import '@nextapps-be/livewire-sortablejs'

// Register Alpine plugins
Alpine.plugin(focus)
Alpine.plugin(collapse) // Register the collapse plugin

// Start Alpine after everything is imported
Alpine.start()