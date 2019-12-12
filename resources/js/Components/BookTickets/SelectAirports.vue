<template>
    <div class="flex">
        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 m-4 text-center w-full">
            <heading class="mb-4">{{ __('From ?') }}</heading>
            <select-airport :options="departureAirports" @change="airport => $emit('selectDeparture', airport)">
                <template #label="{ value }" :for="value">
                    <font-awesome-icon icon="map-marker" color="red" />
                </template>
            </select-airport>
        </div>

        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 m-4 text-center w-full">
            <heading class="mb-4">{{ __('To ?') }}</heading>
            <select-airport :options="availableDestinationAirports" @change="airport => $emit('selectDestination', airport)">
                <template #label="{ value }" :for="value">
                    <font-awesome-icon icon="map-marker" color="green" />
                </template>
            </select-airport>
        </div>
    </div>
</template>

<script>
    import SelectAirport from "~/Components/SelectAirport";
    import Heading from "~/Components/Heading";

    export default {
        name: "SelectAirports",
        components: {Heading, SelectAirport},
        props: {
            departureAirports: Array,
            destinationAirports: Array,
            selectedDepartureAirport: String
        },

        computed: {
            availableDestinationAirports() {
                return this.destinationAirports.filter(airport => airport.IATA !== this.selectedDepartureAirport)
            }
        }
    }
</script>