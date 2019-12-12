<template>
    <div class="bg-white shadow-md rounded">
        <heading class="rounded-t">{{ __('Passengers') }}</heading>

        <div class="flex justify-center">
            <div v-for="(ageGroup, i) in ageGroups" :key="i" class="px-4 m-4 text-center w-1/4">
                <label for="selectPassengers">
                    {{ ageGroup.name }}
                </label>

                <passenger-count
                    :age-group="ageGroup"
                    :selected="selected"
                    :disabled="total >= max"
                    @selectPassengers="(event) => $emit('selectPassengers', event)"
                />
            </div>
        </div>
    </div>
</template>

<script>
    import Heading from "~/Components/Heading";
    import PassengerCount from "./PassengerCount";

    export default {
        name: "SelectPassengers",

        components: {PassengerCount, Heading},

        props: {
            ageGroups: Array,
            selected: Object,
            max: {
                type: Number,
                default: 20
            }
        },

        computed: {
            total() {
                return Object.values(this.selected).reduce((a,b) => a+b, 0)
            }
        },

        mounted() {
            this.$nextTick(() => {
                this.$emit('selectPassengers', {
                    ageGroup: { name: 'Adult' },
                    count: 1
                });
            })
        }
    }
</script>