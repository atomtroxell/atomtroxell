module.exports = {
  content: ['./src/**/*.{njk,md}'],
  plugins: [
    // require('daisyui'),
    require('@tailwindcss/typography')
  ],
  theme: {
    fontFamily: {
      'annie': '"Annie Use Your Telescope", Helvetica, Arial, sans-serif',
      'jakarta': '"Plus Jakarta Sans", Helvetica, Arial, sans-serif',
    },
    colors: {
      'cream': '#fdfbef',
      'swamp': '#9db4a4',
      'dark': '#242422',
      'aura': '#f363db',
      'glow': '#91c29b'
    },
  }
};
