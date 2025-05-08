module.exports = {
  content: ['./src/**/*.{njk,md}'],
  plugins: [
    // require('daisyui'),
    require('@tailwindcss/typography')
  ],
  theme: {
    // fontSize: {
    //   base: '1.25rem',
    // },
    extend: {
      fontFamily: {
        sans: ['"Cantarell"', 'sans-serif'],
        serif: ['"Merriweather"', 'sans-serif'],
        // Set 1
        'merri': '"Merriweather", Palatino, "Palatino Linotype", "Palatino LT STD", "Book Antiqua", Georgia, serif',
        'meera': '"Meera Inimai", "Gill Sans", "Gill Sans MT", Calibri, sans-serif',
        'cantar': '"Cantarell", "Gill Sans", "Gill Sans MT", Calibri, sans-serif',
        'russo': '"Russo One", "Gill Sans", "Gill Sans MT", Calibri, sans-serif',
        // Set 2
        'anton': '"Anton", "Gill Sans", "Gill Sans MT", Calibri, sans-serif',
        'lora': '"Lora", Palatino, "Palatino Linotype", "Palatino LT STD", "Book Antiqua", Georgia, serif',
        // Set 3
        'play': '"Playfair Display", Palatino, "Palatino Linotype", "Palatino LT STD", "Book Antiqua", Georgia, serif',
        'roboto': '"Roboto", "Gill Sans", "Gill Sans MT", Calibri, sans-serif',
        // Alternate Heading or H3
        'bebas': '"Bebas Neue", "Gill Sans", "Gill Sans MT", Calibri, sans-serif',
        'oswald': '"Oswald", "Gill Sans", "Gill Sans MT", Calibri, sans-serif',
      },
      colors: {
        'cream': '#fdfbef',
        'swamp': '#9db4a4',
        'swamp-dark': '#5D7964',
        'dark': '#242422',
        'charcoal': '#595959',
        'aura': '#f363db',
        'aura-dark': '#D20FB1',
        'glow': '#91c29b',
        'glow-dark': '#59A167',
        'mustard': '#e1c15b',
        'mustard-dark': '#8A7019'
      },
      backgroundImage: {
        'divider': "url('/images/hr.webp')",
      },
    },
  },
  // daisyui: {
  //   themes: ["light", "retro"],
  // }
};