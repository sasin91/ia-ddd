module.exports = {
    theme: {
        ripple: theme => ({
            colors: theme('colors'),
        }),

        extend: {
            colors: {
                brand: '#006738',
            }
        }
    },
    variants: {},
    plugins: [
        require('tailwindcss-ripple')(),
    ]
};
