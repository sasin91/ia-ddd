<template>
    <form @submit.prevent="submit">
        <form-field :form="form" attribute="gender">
            <template #input="{ classes }">
                <select v-model="form.gender" :class="classes">
                    <option v-for="(value, description) in genders" :key="value" :value="value"
                            :selected="form.gender === value">
                        {{ description }}
                    </option>
                </select>
            </template>
        </form-field>

        <form-field :form="form" attribute="title">
            <template #input="{ classes }">
                <select v-model="form.title" :class="classes">
                    <option v-for="(value, description) in titles" :key="value" :value="value"
                            :selected="form.title === value">
                        {{ description }}
                    </option>
                </select>
            </template>
        </form-field>

        <form-field
            :form="form"
            attribute="name"
            required
            autocomplete="name"
            autofocus
        />

        <form-field
            :form="form"
            attribute="phone"
            type="tel"
            required
            autocomplete="phone"
        />

        <form-field :form="form" attribute="birthdate">
            <template #input="{ classes }">
                <date-picker
                    v-model="form.birthdate"
                    :class="classes"
                    :maxDate="subYears(new Date(), ageGroup.from)"
                    :minDate="subYears(new Date(), ageGroup.to)"
                    :formatDate="date => date.toDateString()"
                />
            </template>
        </form-field>

        <div class="-mx-3 md:flex mb-6">
            <form-field :form="form" attribute="nationality" class="w-1/2 px-3 mb-6">
                <template #input="{ classes }">
                    <select v-model="form.title" :class="classes">
                        <option v-for="(value, description) in nationalities" :key="value" :value="value"
                                :selected="form.nationality === value">
                            {{ description }}
                        </option>
                    </select>
                </template>
            </form-field>

            <form-field :form="form" attribute="citizenship" class="w-1/2 px-3 mb-6">
                <template #input="{ classes }">
                    <select v-model="form.citizenship" :class="classes">
                        <option v-for="(value, description) in citizenships" :key="value" :value="value"
                                :selected="form.citizenship === value">
                            {{ description }}
                        </option>
                    </select>
                </template>
            </form-field>
        </div>


        <div class="-mx-3 md:flex mb-6">
            <form-field
                class="w-1/2 px-3 mb-6"
                :form="form"
                attribute="passport"
                required
                autocomplete="passport"
            />

            <form-field :form="form" attribute="passport_expires_at" label="Passport Expiration" class="w-1/2 px-3 mb-6">
                <template #input="{ classes }">
                    <date-picker
                        v-model="form.passport_expires_at"
                        :class="classes"
                        :minDate="addMonths(new Date(), 6)"
                        :formatDate="date => date.toDateString()"
                    />
                </template>
            </form-field>
        </div>

        <!-- VISA Block -->

        <button type="submit" :disable="submitting" class="bg-brand text-white font-bold py-2 px-4 rounded w-full">
            {{ __('Done!') }}
            <font-awesome-icon v-show="submitting" icon="spinner" spin />
        </button>
    </form>
</template>

<script>
    import FormField from "~/Components/FormField";
    import DatePicker from "~/Components/DatePicker";
    import {addMonths, subYears} from 'date-fns'

    export default {
        name: "PassengerForm",

        components: {DatePicker, FormField},

        props: {
            form: Object,
            titles: Object,
            genders: Object,
            nationalities: Object,
            citizenships: Object
        },

        data: () => ({
            submitting: false,
        }),

        methods: {
            addMonths,
            subYears,

            async submit() {
                try {
                    this.submitting = true;

                    const { data } = await axios.post(route('passengers.store'), this.form);

                    this.$emit('created', data);
                } finally {
                    this.submitting = false;
                }
            }
        },

        mounted() {
            this.form.age_group = this.ageGroup.name;
            this.form.title = Object.keys(this.titles)[0];
            this.form.gender = Object.keys(this.genders)[0];
            this.form.nationality = Object.keys(this.nationalities)[0];
            this.form.citizenship = Object.keys(this.citizenships)[0];

            if (this.$page.auth) {
                this.form.phone = this.$page.auth.phone;
            }
        }
    }
</script>