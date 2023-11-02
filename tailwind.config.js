/** @type {import('tailwindcss').Config} */
module.exports = {
  mode: 'jit',
  content: ['./content/**/*.md', './layouts/**/*.html'],
  theme: {
    extend: {},
  },
  plugins: [require('@tailwindcss/typography')],
}

