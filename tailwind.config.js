import defaultTheme from 'tailwindcss/defaultTheme'
import forms from '@tailwindcss/forms'

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
  ],
  darkMode: 'media', // or 'class' if you want to toggle manually
  theme: {
    extend: {
      colors: {
        brand: {
          DEFAULT: '#10b981',   // Emerald 500
          dark: '#047857',      // Emerald 700
          light: '#6ee7b7',     // Emerald 300
        },
        secondary: {
          DEFAULT: '#6b7280',   // Gray 500
          dark: '#374151',      // Gray 700
          light: '#d1d5db',     // Gray 300
        },
        background: {
          DEFAULT: '#ffffff',
          secondary: '#f3f4f6', // Gray 100
          dark: '#111827',      // Gray 900 (if dark mode later)
        },
        text: {
          DEFAULT: '#1f2937',   // Gray 800
          secondary: '#6b7280', // Gray 500
          inverted: '#ffffff',  // White text on dark bg
        },
        error: '#ef4444', // Red 500
      },
      fontFamily: {
        display: ['Poppins', ...defaultTheme.fontFamily.sans],
        body: ['Inter', ...defaultTheme.fontFamily.sans],
      },
      borderRadius: {
        md: '0.5rem',
        xl: '0.75rem',
        '2xl': '1rem',
      },
      boxShadow: {
        sm: '0 1px 2px rgba(0, 0, 0, 0.05)',
        md: '0 4px 6px rgba(0, 0, 0, 0.1)',
        lg: '0 10px 15px rgba(0, 0, 0, 0.15)',
      },
      zIndex: {
        modal: 999,
        overlay: 998,
      },
    },
  },
  plugins: [forms],
}
