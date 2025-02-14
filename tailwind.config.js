/** @type {import('tailwindcss').Config} */
const colors = require('tailwindcss/colors')
const plugin = require('tailwindcss/plugin')

module.exports = {
  content: [
      './lib/**/*.{html,js,html.twig}',
  ], 
    plugins: [
    plugin(function ({ addVariant }) {
        addVariant('hocus', ['&:hover', '&:focus'])
    })
  ],
  theme: {
    extend: {
        screens: {
            'xs': '360px',
            'sm': '480px',
            'md': '768px',
            'lg': '976px',
            'xl': '1325px',
            '2xl': '1440px',
            '3xl': '1880px',
            '4xl': '2080px',
            '5xl': '2526px',
            '6xl': '3080px',
            '7xl': '3580px',
            '8xl': '3840px',
        },
        colors: {
            transparent: 'transparent',
            current: 'currentColor',
            gray: colors.slate,
            black: colors.black,
            white: colors.white,
            'base': '#f4f6ff',
            'primary': {
                DEFAULT: '#009383',
                50: '#eefffa',
                100: '#c5fff3',
                200: '#8bffe8',
                300: '#4afedc',
                400: '#15ecca',
                500: '#15ecca',
                600: '#00a893',
                700: '#00a893',
                800: '#066960',
                900: '#0a574f',
                950: '#003532',
            },
        },
        spacing: {
            '1': '4px',
            '2': '8px',
            '3': '12px',
            '4': '16px',
            '5': '24px',
            '6': '32px',
            '7': '48px',
        },
        maxWidth: {
            '8xl': '3840px',
        },
        fontWeight: {
            hairline: '100',
            thin: '200',
            light: '300',
            normal: '400',
            medium: '500',
            semibold: '600',
            bold: '700',
            extrabold: '800',
            black: '900',
        },
        blur: {
            xs: '2px',
        }
    },
  },
}

