const subdomain = process.env.SUBDOMAIN || 'default';

module.exports = {
  plugins: [
    require('tailwindcss'),
    require('autoprefixer'),
  ],
};