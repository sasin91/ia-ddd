<template>
    <layout title="Booking">
        <select-airports
            :departure-airports="departureAirports"
            :destination-airports="destinationAirports"
            :selected-departure-airport="airports.selectedDeparture"
            @selectDeparture="airport => airports.selectedDeparture = airport"
            @selectDestination="airport => airports.selectedDestination = airport"
        />

        <select-travels
            :class="readyToSelectDepartures ? null : ['opacity-50', 'pointer-events-none']"
            :travel-periods="travelPeriods"
            :outward-travels="outwardTravels"
            :home-travels="homeTravels"
            :selected-travel-period="selectedTravelPeriod"
            :selected-outward-date="selectedOutwardDate"
            @selectTravelPeriod="travelPeriod => selectedTravelPeriod = travelPeriod"
            @selectOutwardDate="outwardDate => selectedOutwardDate = outwardDate"
            @selectHomeDate="homeDate => selectedHomeDate = homeDate"
        />

        <div :class="readyToConfigurePassengers ? null : ['opacity-50', 'pointer-events-none']">
            <!-- Using this.$set to make newly assigned props of selectedPassengers reactive -->
            <select-passengers
                :age-groups="ageGroups"
                :selected="selectedPassengers"
                @selectPassengers="({ ageGroup, count }) => $set(selectedPassengers, ageGroup.name, parseInt(count))"
            />

            <div class="flex justify-center mt-4" :class="{ 'flex-wrap': totalPassengers >= 3 }" v-for="(count, ageGroup) in selectedPassengers" :key="ageGroup">
                <docked-passenger
                    :class="totalPassengers >= 3 ? 'md:w-1/4 w-1/2' : 'w-full'"
                    class="px-8 pt-6 pb-8 mx-2 my-2"
                    v-for="number in count"
                    :key="`${ageGroup}-${number}`"
                    :number="number"
                    :age-group="ageGroups.find(a => a.name === ageGroup)"
                    @editPassenger="passenger => editingPassenger = passenger"
                />
            </div>
        </div>

        <modal :visible="editingPassenger !== null" @close="editingPassenger = null">
            <passenger-form
                v-if="editingPassenger"
                :form="passengerForms.find(form => form.ageGroup.name === editingPassenger.ageGroup.name && form.number === editingPassenger.number)"
                :titles="passengerTitles"
                :genders="passengerGenders"
                :nationalities="passengerNationalities"
                :citizenships="passengerCitizenships"
            />
        </modal>
    </layout>
</template>

<script>
    import Layout from "~/Shared/Layout";
    import SelectAirports from "~/Components/BookTickets/SelectAirports";
    import SelectTravels from "~/Components/BookTickets/SelectTravels";
    import SelectPassengers from "~/Components/BookTickets/SelectPassengers";
    import DockedPassenger from "~/Components/BookTickets/DockedPassenger";
    import Modal from "~/Components/Modal";
    import PassengerForm from "~/Components/PassengerForm";

    export default {
        name: "BookTickets",
        components: {PassengerForm, Modal, DockedPassenger, SelectPassengers, SelectTravels, SelectAirports, Layout},
        props: {
            departureAirports: Array,
            destinationAirports: Array,

            travelPeriods: Object,
            outwardTravels: Array,
            homeTravels: Array,

            ageGroups: Array,
            passengerTitles: Object,
            passengerGenders: Object,
            passengerNationalities: Object,
            passengerCitizenships: Object
        },

        data: () => ({
            airports: {
                selectedDeparture: null,
                selectedDestination: null,
            },
            selectedOutwardDate: null,
            selectedHomeDate: null,
            selectedTravelPeriod: 'one-way',

            selectedPassengers: {
                // Infant: 0,
                // Child: 0,
                // Adult: 0
            },

            editingPassenger: null
        }),

        computed: {
            totalPassengers() {
                return Object.values(this.selectedPassengers).reduce((a,b) => a+b, 0)
            },

            passengerForms() {
                const forms = [];

                Object.keys(this.selectedPassengers).forEach(ageGroupName => {
                    for (let i = 1; i < this.selectedPassengers[ageGroupName]+1; i++) {
                        forms.push({
                            ageGroup: this.ageGroups.find(a => a.name === ageGroupName),
                            number: i,
                            form: {
                                age_group: ageGroupName,
                                title: '',
                                name: '',
                                gender: '',
                                phone: '',
                                birthdate: '',
                                nationality: '',
                                citizenship: '',
                                passport: '',
                                passport_expires_at: '',
                                visa: '',
                                visa_country: '',
                                visa_expires_at: '',
                            }
                        })
                    }
                });

                return forms;
            },

            readyToSelectDepartures() {
                return this.travelPeriods
                    && this.outwardTravels.length > 0
            },

            readyToConfigurePassengers() {
                if (!this.selectedOutwardDate) {
                    return false;
                }

                if (this.selectedTravelPeriod === 'one-way') {
                    return true;
                }

                return !!this.selectedHomeDate
            }
        },

        watch: {
            airports: {
                handler: function ({ selectedDeparture, selectedDestination }) {
                    if (selectedDeparture && selectedDestination) {
                        this.$inertia.visit(
                            route('book-tickets.show-travels', {
                                departure: selectedDeparture,
                                destination: selectedDestination
                            }), {
                                preserveState: true
                            }
                        )
                    }
                },

                deep: true
            }
        }
    }
</script>