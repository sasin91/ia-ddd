<template>
    <flat-pickr
        ref="flatPickr"
        class="appearance-none bg-transparent border-b-2 border-brand text-center text-gray-700 mr-3 py-1 px-2 leading-tight focus:outline-none"
        :config="config"
        :value="value"
        @input="value => $emit('change', value)"
    />
</template>

<script>
    import flatPickr from 'vue-flatpickr-component';
    import 'flatpickr/dist/flatpickr.css';
    import 'flatpickr/dist/themes/material_green.css'

    /**
     * Unbound props will be passed as config for flat-pickr.
     * @see https://flatpickr.js.org/options/
     */
    export default {
        model: {
            prop: 'value',
            event: 'change'
        },

        name: "DatePicker",

        components: {
            flatPickr
        },

        props: {
            value: [String,Date, Array],
        },

        computed: {
            config() {
                return Object.assign({}, this.$attrs, {
                    wrap: true
                })
            }
        },

        methods: {
            toggle() {
                this.$refs.flatPickr.fp.toggle()
            },

            open() {
                this.$refs.flatPickr.fp.open()
            }
        }
    }
</script>