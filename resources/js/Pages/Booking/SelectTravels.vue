<template>
    <layout :title="`Booking :: ${departure} - ${destination}`">
        <breadcrumbs>
            <template #home-link>
                <inertia-link href="/">
                    <font-awesome-icon icon="plane-departure" size="2x" class="text-brand" />
                </inertia-link>
            </template>

            <template #links="{ URLFragments, classes }">
                <li class="text-brand text-bold" :class="classes">
                    {{ URLFragments.join('-') }}
                </li>
            </template>
        </breadcrumbs>

    <div class="w-full max-w-full flex justify-center">
        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 m-4 text-center" @click="$refs.outwardCalendar.open()">
            <heading class="mb-4">{{ __('Outward') }}</heading>
            <font-awesome-icon icon="calendar" class="fill-current text-brand m-4" size="6x" />
            <date-picker
                class="w-full"
                ref="outwardCalendar"
                v-model="outwardDate"
                :enable="outwardTravelDates"
                :formatDate="(date) => date.toDateString()"
            />
        </div>

        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 m-4 text-center">
            <heading class="mb-4">{{ __('Type') }}</heading>
            <div class="flex flex-col">
                <travel-type
                    v-tooltip="__('Only one way outward')"
                    @click="travelType = 'oneWay'"
                    :is-selected="travelType === 'oneWay'"
                >
                    <font-awesome-icon icon="arrow-right" class="fill-current text-brand" size="3x" />
                </travel-type>

                <travel-type
                    v-tooltip="__('Outward + Scheduled home')"
                    @click="travelType = 'roundTrip'"
                    :is-selected="travelType === 'roundTrip'"
                >
                    <font-awesome-icon icon="exchange" class="fill-current text-brand" size="3x" />
                </travel-type>

                <travel-type
                    v-tooltip="__('Outward + Flexible choice of home departure')"
                    @click="travelType = 'flex'"
                    :is-selected="travelType === 'flex'"
                >
                    <font-awesome-icon :icon="['fad', 'exchange']" class="fill-current text-brand" size="3x" />
                </travel-type>
            </div>
        </div>

        <div
            class="bg-white rounded px-8 pt-6 pb-8 m-4 text-center"
            @click="travelType === 'roundTrip' && outwardDate ? $refs.homeCalendar.open() : null"
            :class="travelType === 'roundTrip' && outwardDate ? ['shadow-md'] : ['bg-gray-300', 'opacity-50']"
            v-tooltip="travelType !== 'roundTrip' ? __('Home date is only available for round trips.') : null"
        >
            <heading class="mb-4">{{ __('Home') }}</heading>
            <font-awesome-icon icon="calendar" class="fill-current text-brand m-4" size="6x" />
            <date-picker
                class="w-full"
                :class="travelType !== 'roundTrip' || !outwardDate ? 'pointer-events-none' : null"
                ref="homeCalendar"
                v-model="homeDate"
                :enable="homeTravelDates"
                :formatDate="date => date.toDateString()"
            />
        </div>
    </div>
    </layout>
</template>

<script>
    import DatePicker from "~/Components/DatePicker";
    import Heading from "~/Components/Heading";
    import TravelType from "~/Components/TravelType";
    import Layout from "~/Shared/Layout";
    import Breadcrumbs from "~/Components/Breadcrumbs";
    import { isSameDay } from 'date-fns'

    export default {
        name: "SelectTravelsAndType",

        components: {Breadcrumbs, Layout, TravelType, Heading, DatePicker},

        props: {
            departure: String,
            destination: String,
            travelPeriods: Object,
            outwardTravels: Array,
            homeTravels: Array
        },

        computed: {
            outwardTravelDates() {
                return this.outwardTravels.flatMap(travel => travel.available_timestamps.map(ts => ts[0]))
            },

            homeTravelDates() {
                if (this.outwardDate) {
                    return this
                        .homeTravels
                        .flatMap(travel => travel.available_timestamps.map(ts => ts[0]))
                        .filter(timestamp => isSameDay(new Date(timestamp), new Date(this.outwardDate)) === false)
                }

                return []
            }
        },

        data: () => ({
            outwardDate: null,
            homeDate: null,
            travelType: 'oneWay'
        })
    }
</script>