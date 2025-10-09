/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/views/**/*.blade.php",
    "./resources/js/**/*.js",
    "./vendor/filament/**/*.blade.php", // Filament views
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}
