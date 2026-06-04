/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        // Primary Colors (Red & Yellow Theme)
        'primary': {
          '50': '#fef2f2',
          '100': '#fee2e2',
          '200': '#fecaca',
          '300': '#fca5a5',
          '400': '#f87171',
          '500': '#ef4444',
          '600': '#dc2626',
          '700': '#b91c1c',
          '800': '#991b1b',
          '900': '#7f1d1d',
        },
        'secondary': {
          '50': '#fffbeb',
          '100': '#fef3c7',
          '200': '#fde68a',
          '300': '#fcd34d',
          '400': '#fbbf24',
          '500': '#f59e0b',
          '600': '#d97706',
          '700': '#b45309',
          '800': '#92400e',
          '900': '#78350f',
        },
        // Navy Blue for Footer
        'navy': {
          '50': '#eff6ff',
          '100': '#dbeafe',
          '200': '#bfdbfe',
          '300': '#93c5fd',
          '400': '#60a5fa',
          '500': '#3b82f6',
          '600': '#2563eb',
          '700': '#1d4ed8',
          '800': '#1e40af',
          '900': '#1e3a8a',
        },
        // Custom Restaurant Colors
        'restaurant': {
          'red': '#DC2626',
          'yellow': '#FBBF24',
          'navy': '#1E3A8A',
          'light': '#FFF9F0',
          'dark': '#1F2937',
        }
      },
      fontFamily: {
        'sans': ['Poppins', 'system-ui', '-apple-system', 'sans-serif'],
        'display': ['Playfair Display', 'serif'],
        'mono': ['JetBrains Mono', 'monospace'],
      },
      animation: {
        // Basic Animations
        'fade-in': 'fadeIn 0.5s ease-in-out',
        'fade-out': 'fadeOut 0.5s ease-in-out',
        'slide-up': 'slideUp 0.3s ease-out',
        'slide-down': 'slideDown 0.3s ease-out',
        'slide-left': 'slideLeft 0.3s ease-out',
        'slide-right': 'slideRight 0.3s ease-out',

        // Custom Restaurant Animations
        'pulse-slow': 'pulse 3s infinite',
        'bounce-slow': 'bounce 2s infinite',
        'spin-slow': 'spin 3s linear infinite',
        'ping-slow': 'ping 2s cubic-bezier(0, 0, 0.2, 1) infinite',

        // Menu Item Animations
        'menu-item': 'menuItem 0.5s ease-out',
        'flavor-reveal': 'flavorReveal 0.5s ease-out forwards',
        'cart-add': 'cartAdd 0.3s ease-out',

        // Loading Animations
        'loading-dots': 'loadingDots 1.4s infinite',
        'loading-spinner': 'loadingSpinner 1.2s linear infinite',
      },
      keyframes: {
        fadeIn: {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' },
        },
        fadeOut: {
          '0%': { opacity: '1' },
          '100%': { opacity: '0' },
        },
        slideUp: {
          '0%': { transform: 'translateY(20px)', opacity: '0' },
          '100%': { transform: 'translateY(0)', opacity: '1' },
        },
        slideDown: {
          '0%': { transform: 'translateY(-20px)', opacity: '0' },
          '100%': { transform: 'translateY(0)', opacity: '1' },
        },
        slideLeft: {
          '0%': { transform: 'translateX(20px)', opacity: '0' },
          '100%': { transform: 'translateX(0)', opacity: '1' },
        },
        slideRight: {
          '0%': { transform: 'translateX(-20px)', opacity: '0' },
          '100%': { transform: 'translateX(0)', opacity: '1' },
        },

        // Custom Animations
        menuItem: {
          '0%': { transform: 'scale(0.9)', opacity: '0' },
          '100%': { transform: 'scale(1)', opacity: '1' },
        },
        flavorReveal: {
          '0%': { transform: 'translateX(-100%)', opacity: '0' },
          '100%': { transform: 'translateX(0)', opacity: '1' },
        },
        cartAdd: {
          '0%': { transform: 'scale(1)' },
          '50%': { transform: 'scale(1.1)' },
          '100%': { transform: 'scale(1)' },
        },
        loadingDots: {
          '0%, 80%, 100%': { opacity: '0' },
          '40%': { opacity: '1' },
        },
        loadingSpinner: {
          '0%': { transform: 'rotate(0deg)' },
          '100%': { transform: 'rotate(360deg)' },
        },
      },
      backgroundImage: {
        'gradient-restaurant': 'linear-gradient(135deg, #DC2626 0%, #FBBF24 100%)',
        'gradient-nav': 'linear-gradient(90deg, #1E3A8A 0%, #2563EB 100%)',
        'gradient-menu': 'linear-gradient(180deg, #FFF9F0 0%, #FFFFFF 100%)',
      },
      boxShadow: {
        'restaurant': '0 10px 25px -5px rgba(220, 38, 38, 0.1), 0 10px 10px -5px rgba(220, 38, 38, 0.04)',
        'menu-card': '0 20px 40px -15px rgba(0, 0, 0, 0.1)',
        'floating': '0 35px 60px -15px rgba(0, 0, 0, 0.3)',
      },
      borderRadius: {
        'restaurant': '1rem',
        'menu-item': '0.75rem',
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
    require('@tailwindcss/aspect-ratio'),
  ],
}
