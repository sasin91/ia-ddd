<template>
    <layout title="Booking :: Select Airports">
        <div class="flex">
            <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 m-4 text-center w-full">
                <heading class="mb-4">{{ __('From ?') }}</heading>
                <select-airport :options="departureAirports" v-model="selectedDeparture">
                    <template #label="{ value }" :for="value">
                        <font-awesome-icon icon="map-marker" color="red" />
                    </template>
                </select-airport>
            </div>

            <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 m-4 text-center w-full">
                <heading class="mb-4">{{ __('To ?') }}</heading>
                <select-airport :options="availableDestinationAirports" v-model="selectedDestination">
                    <template #label="{ value }" :for="value">
                        <font-awesome-icon icon="map-marker" color="green" />
                    </template>
                </select-airport>
            </div>
        </div>
    </layout>
</template>

<script>
    import Layout from "~/Shared/Layout";
    import SelectAirport from "~/Components/SelectAirport";
    import Heading from "~/Components/Heading";

    export default {
        name: "SelectAirports",

        components: {
            Layout,
            SelectAirport,
            Heading
        },

        props: {
            departureAirports: Array,
            destinationAirports: Array,
        },

        computed: {
            availableDestinationAirports() {
                return this.destinationAirports.filter(airport => airport.IATA !== this.selectedDepartureAirport)
            }
        },

        watch: {
            selectedDestination: {
                handler(destination) {
                    if (destination && this.selectedDeparture) {
                        this.showTravels()
                    }
                }
            }
        },

        data: () => ({
            selectedDeparture: null,
            selectedDestination: null,
        }),

        methods: {
            showTravels() {
                this.$inertia.visit(
                    route('book-tickets.show-travels', {
                        departure: this.selectedDeparture,
                        destination: this.selectedDestination
                    }), {
                        preserveState: true
                    }
                )
            }
        }
    }
</script>