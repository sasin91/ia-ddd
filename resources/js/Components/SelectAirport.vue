<template>
    <div class="relative">
        <label :for="name" class="absolute inset-y-0 left-0 flex items-center px-2">
            <slot :value="name" name="label">
                {{ __(label || 'Select Airport') }}
            </slot>
        </label>

        <select v-model="selected" :id="name" class="block appearance-none w-full bg-white border border-gray-400 hover:border-gray-500 p-4 rounded shadow leading-tight focus:outline-none focus:shadow-outline">
            <option v-for="airport in options" :key="airport.id" :value="airport.IATA" :selected="selected === airport.IATA">
                {{ airport.location }},
                {{ airport.country }}
            </option>
        </select>
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
            <font-awesome-icon icon="chevron-down" />
        </div>
    </div>
</template>

<script>
    export default {
        model: {
            prop: 'selected',
            event: 'change'
        },

        props: {
            options: Array,
            value: [String,Object],
            label: String
        },

        data: () => ({
            selected: null
        }),

        computed: {
            name() {
                return `${this.$options._componentTag}-${this._uid}`
            }
        },

        watch: {
            selected(value) {
                if (value) {
                    this.$emit('change', value)
                }
            }
        },

        mounted() {
            this.selected = this.value
        }
    }
</script>