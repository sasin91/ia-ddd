<template>
    <select @change="(event) => $emit('selectPassengers', { ageGroup, count: event.target.value })"
            id="selectPassengers"
            class="block appearance-none w-full bg-white border border-gray-400 hover:border-gray-500 rounded shadow leading-tight text-center focus:outline-none focus:shadow-outline py-4">
        <option value="0" :selected="selected[ageGroup.name] && selected[ageGroup] === 0">0</option>
        <option v-for="count in limit" :key="count" :value="count"
                :selected="selected[ageGroup.name] && selected[ageGroup.name] === count">
            {{ count }}
        </option>
    </select>
</template>
<script>
    export default {
        name: 'passenger-count',
        props: {
            ageGroup: Object,
            selected: Object,
        },

        computed: {
            count() {
                return this.selected[this.ageGroup.name] || 0
            },

            limit() {
                if (this.ageGroup.name.toLowerCase() === 'infant') {
                    return parseInt(this.selected['Adult'] || 0);
                }

                return parseInt(this.ageGroup.passenger_limit);
            }
        }
    }
</script>