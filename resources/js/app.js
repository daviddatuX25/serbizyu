// resources/js/app.js
import './bootstrap';

// Import Alpine core and plugins first
import Alpine from 'alpinejs'
import focus from '@alpinejs/focus'
// import magicHelpers from '@ryangjchandler/alpinejs-magic-helpers' // optional

// Register plugins
Alpine.plugin(focus)
// Alpine.plugin(magicHelpers) // optional
// Start Alpine
Alpine.start()
