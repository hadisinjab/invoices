/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      screens: {
        'xs': '475px',
        'sm': '640px',
        'md': '768px',
        'lg': '1024px',
        'xl': '1280px',
        '2xl': '1536px',
      },
      fontFamily: {
        'cairo': ['Cairo', 'sans-serif'],
      },
      direction: {
        'rtl': 'rtl',
        'ltr': 'ltr',
      },
      spacing: {
        '18': '4.5rem',
        '88': '22rem',
        '128': '32rem',
      },
      colors: {
        primary: {
          50: '#f0f4ff',
          100: '#e0e9ff',
          500: '#667eea',
          600: '#5a67d8',
          700: '#4c51bf',
        },
        secondary: {
          50: '#fdf2f8',
          100: '#fce7f3',
          500: '#764ba2',
          600: '#6b46c1',
          700: '#553c9a',
        }
      }
    },
  },
  plugins: [],
}
