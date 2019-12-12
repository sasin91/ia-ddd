<template>
    <div class="w-full max-w-full flex justify-center">
        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 m-4 text-center" @click="$refs.outwardCalendar.open()">
            <heading class="mb-4">{{ __('Outward') }}</heading>
            <font-awesome-icon icon="calendar" class="fill-current text-brand m-4" size="6x" />
            <date-picker
                class="w-full"
                ref="outwardCalendar"
                @change="outwardDate => $emit('selectOutwardDate', outwardDate)"
                :enable="outwardDepartureDates"
                :formatDate="(date) => date.toDateString()"
            />
        </div>

        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 m-4 text-center">
            <heading class="mb-4">{{ __('Period') }}</heading>
            <div class="flex flex-col">
                <travel-period
                    v-tooltip="__('Only one way outward')"
                    @click="$emit('selectTravelPeriod', 'one-way')"
                    :is-selected="selectedTravelPeriod === 'one-way'"
                >
                    <font-awesome-icon icon="arrow-right" class="fill-current text-brand" size="3x" />
                </travel-period>

                <travel-period
                    v-tooltip="__('Outward + Scheduled home')"
                    @click="$emit('selectTravelPeriod', 'round-trip')"
                    :is-selected="selectedTravelPeriod === 'round-trip'"
                >
                    <font-awesome-icon icon="exchange" class="fill-current text-brand" size="3x" />
                </travel-period>

                <travel-period
                    v-tooltip="__('Outward + Flexible choice of home departure')"
                    @click="$emit('selectTravelPeriod', 'flex')"
                    :is-selected="selectedTravelPeriod === 'flex'"
                >
                    <font-awesome-icon :icon="['fad', 'exchange']" class="fill-current text-brand" size="3x" />
                </travel-period>
            </div>
        </div>

        <div
            class="bg-white rounded px-8 pt-6 pb-8 m-4 text-center"
            @click="selectedTravelPeriod === 'round-trip' && selectedOutwardDate ? $refs.homeCalendar.open() : null"
            :class="selectedTravelPeriod === 'round-trip' && selectedOutwardDate ? ['shadow-md'] : ['bg-gray-300', 'opacity-50']"
            v-tooltip="selectedTravelPeriod !== 'round-trip' ? __('Home date is only available for round trips.') : null"
        >
            <heading class="mb-4">{{ __('Home') }}</heading>
            <font-awesome-icon icon="calendar" class="fill-current text-brand m-4" size="6x" />
            <date-picker
                class="w-full"
                :class="selectedTravelPeriod !== 'round-trip' || !selectedOutwardDate ? 'pointer-events-none' : null"
                ref="homeCalendar"
                @change="homeDate => $emit('selectHomeDate', homeDate)"
                :enable="homeDepartureDates"
                :formatDate="date => date.toDateString()"
            />
        </div>
    </div>
</template>

<script>
    import DatePicker from "~/Components/DatePicker";
    import Heading from "~/Components/Heading";
    import TravelPeriod from "~/Components/TravelType";
    import { differenceInDays } from 'date-fns'

    export default {
        name: "SelectTravels",
        components: {TravelPeriod, Heading, DatePicker},
        props: {
            travelPeriods: {
                type: Object,
                default() {
                    return {}
                }
            },

            selectedTravelPeriod: String,
            selectedOutwardDate: String,

            outwardTravels: {
                type: Array,
                default() {
                    return [];
                }
            },

            homeTravels: {
                type: Array,
                default() {
                    return [];
                }
            }
        },

        computed: {
            outwardDepartureDates() {
                if (this.outwardTravels.length > 0) {
                    return this.outwardTravels[0].available_timestamps.map(timestamps => timestamps[0])
                }

                return []
            },

            homeDepartureDates() {
                if (this.homeTravels.length > 0 && this.selectedOutwardDate) {
                    return this
                        .homeTravels[0]
                        .available_timestamps
                        .map(timestamps => timestamps[0])
                        .filter(homeDateTime => differenceInDays(new Date(homeDateTime), new Date(this.selectedOutwardDate)) > 0)
                }

                return []
            }
        }
    }
</script>