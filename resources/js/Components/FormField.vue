<template>
    <div class="flex flex-wrap mb-6">
        <slot class="labelClasses" name="label">
            <label :classes="labelClasses" :for="attribute" :value="labelValue">
                {{ __(labelValue) }}
            </label>
        </slot>

        <slot :attribute="attribute" :classes="inputClasses" :form="form" :hasErrors="hasErrors" name="input">
            <input
                v-bind="$attrs"
                :id="attribute"
                :name="attribute"
                :class="inputClasses"
                v-model="form[attribute]"
            >
        </slot>

        <slot :has-errors="hasErrors" errors="errors" name="errors">
            <ul class="text-red-500 text-xs italic mt-4" v-show="hasErrors">
                <li v-for="error in errors">{{ error }}</li>
            </ul>
        </slot>
    </div>
</template>

<script>
    import {startCase} from 'lodash'

    export default {
        name: "FormField",

        props: {
            form: Object,
            attribute: String,
            label: String
        },

        computed: {
            errors() {
                return this.$page.errors[this.attribute]
            },

            hasErrors() {
                return Array.isArray(this.errors)
            },

            inputClasses() {
                let classes = "shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline";

                if (this.hasErrors) {
                    classes += 'border-red-500'
                }

                return classes
            },

            labelClasses() {
                return "block text-gray-700 text-sm font-bold mb-2"
            },

            labelValue() {
                return this.label || startCase(this.attribute)
            }
        },

        methods: {
            startCase
        }
    }
</script>
