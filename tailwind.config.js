/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './*.{php,html}',
    './includes/**/*.php',
    './assets/js/**/*.js',
  ],
  theme: {
    extend: {
      colors: {
        rose: {
          50: '#fff1f2',
          100: '#ffe4e6',
          200: '#fecdd3',
          300: '#fda4af',
          400: '#e85d75',
          500: '#d14860',
          600: '#b13c51',
        },
        warm: {
          50: '#fff8f5',
          100: '#fef0ec',
          200: '#fde0d8',
        },
      },
      fontFamily: {
        quicksand: ['Quicksand', 'sans-serif'],
        inter: ['Inter', 'sans-serif'],
      },
    },
  },
  plugins: [],
};
