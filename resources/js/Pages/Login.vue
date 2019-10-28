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
                                attribute="email"
                                label="E-Mail Address"
                                type="password"
                                required
                                autocomplete="email"
                                autofocus
                            />

                            <form-field
                                :form="form"
                                attribute="password"
                                type="password"
                                required
                            />

                            <div class="flex mb-6">
                                <input type="checkbox" name="remember" id="remember" v-model="form.remember">

                                <label class="text-sm text-gray-700 ml-3" for="remember">
                                    {{ __('Remember Me') }}
                                </label>
                            </div>

                            <div class="flex flex-wrap items-center">
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-gray-100 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                    {{ __('Login') }}
                                </button>

                                <inertia-link href="password/reset" class="text-sm text-blue-500 hover:text-blue-700 whitespace-no-wrap no-underline ml-auto">
                                    {{ __('Forgot Your Password?') }}
                                </inertia-link>

                                <p class="w-full text-xs text-center text-gray-700 mt-8 -mb-4">
                                    {{ __("Don't have an account?") }}
                                    <inertia-link class="text-blue-500 hover:text-blue-700 no-underline" href="register">
                                        {{ __('Register') }}
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
                password: '',
                remember: true
            }
        }),

        methods: {
            submit() {
                this.$inertia.post('/login', this.form)
            },
        }
    }
</script>
