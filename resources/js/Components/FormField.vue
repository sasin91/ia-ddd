<template>
    <div class="flex flex-wrap mb-6">
        <slot name="label">
            <label :for="attribute" class="block text-gray-700 text-sm font-bold mb-2">
                {{ __(label || startCase(attribute)) }}
            </label>
        </slot>

        <slot name="input" :attribute="attribute" :form="form" :hasErrors="hasErrors">
            <input
                v-bind="$attrs"
                :id="attribute"
                :name="attribute"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                :class="{ 'border-red-500': hasErrors }"
                v-model="form[attribute]"
            >
        </slot>

        <ul v-show="hasErrors" class="text-red-500 text-xs italic mt-4">
            <li v-for="error in errors">{{ error }}</li>
        </ul>
    </div>
</template>

<script>
    import { startCase } from 'lodash'

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
            }
        },

        methods: {
            startCase
        }
    }
</script>
