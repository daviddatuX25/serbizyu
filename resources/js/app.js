// resources/js/app.js
import './bootstrap';

// Import Alpine core and plugins
import Alpine from 'alpinejs'
import focus from '@alpinejs/focus'

// Import Livewire 4 sortable (replaces old livewire-sortablejs)
import '@nextapps-be/livewire-sortablejs'

// Register Alpine plugins
Alpine.plugin(focus)

// Start Alpine after everything is imported
Alpine.start()