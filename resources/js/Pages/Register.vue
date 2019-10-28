<template>
    <layout>
        <div class="container mx-auto">
            <div class="flex flex-wrap justify-center">
                <div class="w-full max-w-sm">
                    <div class="flex flex-col break-words bg-white border border-2 rounded shadow-md">

                        <heading>
                            {{ __('Login') }}
                        </heading>

                        <form class="w-full p-6" method="POST" @submit.prevent="submit">
                            <form-field
                                :form="form"
                                attribute="name"
                                required
                                autocomplete="name"
                                autofocus
                            />
                            <form-field
                                :form="form"
                                attribute="username"
                                required
                                autocomplete="username"
                            />

                            <form-field
                                :form="form"
                                attribute="email"
                                label="E-Mail Address"
                                type="email"
                                required
                                autocomplete="email"
                            />

                            <form-field
                                :form="form"
                                attribute="password"
                                type="password"
                                required
                            />

                            <form-field
                                :form="form"
                                attribute="password_confirmation"
                                type="password"
                                label="Confirm password"
                                required
                            />

                            <div class="flex flex-wrap">
                                <button type="submit" class="inline-block align-middle text-center select-none border font-bold whitespace-no-wrap py-2 px-4 rounded text-base leading-normal no-underline text-gray-100 bg-blue-500 hover:bg-blue-700">
                                    {{ __('Register') }}
                                </button>

                                <p class="w-full text-xs text-center text-gray-700 mt-8 -mb-4">
                                    {{ __('Already have an account?') }}
                                    <inertia-link class="text-blue-500 hover:text-blue-700 no-underline" href="login">
                                        {{ __('Login') }}
                                    </inertia-link>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </layout>
</template>

<script>
    import Layout from '~/Shared/Layout'
    import Heading from "~/Components/Heading";
    import FormField from "../Components/FormField";

    export default {
        components: {
            FormField,
            Heading,
            Layout,
        },

        data: () => ({
            form: {
                email: '',
                username: '',
                password: '',
                password_confirmation: ''
            }
        }),

        methods: {
            submit() {
                this.$inertia.post('/register', this.form)
            },
        }
    }
</script>
