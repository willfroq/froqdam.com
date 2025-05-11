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
                '96': '96px',
                '42': '42px',
                '46': '46px',
                '52': '52px',
                '760': '760px',
                '280': '280px',
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
            },
            fontSize: {
                '36': '36px',
                '28': '28px',
                '24': '24px',
                '22': '22px',
                '20': '20px',
                '18': '18px',
                '16': '16px',
                '14': '14px',
                '12': '12px',
            },
            lineHeight: {
                'leading-52': '52px',
                'leading-32': '32px',
                'leading-30': '30px',
                'leading-28': '28px',
                'leading-26': '26px',
                'leading-24': '24px',
                'leading-20': '20px',
                'leading-18': '18px',
                'leading-16': '16px',
                'leading-14': '14px',
                'leading-12': '12px',
            },
            borderRadius: {
                'lg': '8px',
                'full': '50px',
            },
            gap: {
                '96': '96px',
                '24': '24px',
                '16': '16px',
                '15': '15px',
                '8': '8px',
                '5': '5px',
                '1': '1px',
            },
            height: {
                '96': '96px',
                '40': '40px',
                '80': '80px',
                '42': '42px',
                '34': '34px',
                '46': '46px',
                '52': '52px',
                '760': '760px',
            },
            letterSpacing: {
                'normal': '0%',
            },
            minWidth: {
                '224': '224px',
            },
            width: {
                '42': '42px',
                '34': '34px',
                '56': '56px',
                '284': '284px',
                '80': '80px',
                '100': '100px',
                '46': '46px',
            },
            paddingLeft: {
                '42': '42px',
            },
            paddingRight: {
                '10': '10px',
            },
            fontFamily: {
                sans: 'Open Sans',
            },
        },
    },
}

