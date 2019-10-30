<template>
    <header class="bg-brand text-white shadow">
        <div class="w-full max-w-6xl mx-auto sm:flex sm:justify-between sm:items-center sm:px-4 sm:py-3">
            <div class="flex justify-between items-center px-4 py-3 sm:p-0">
                <inertia-link class="navbar-brand" href="/">
                    <img alt="Company logo" src="/storage/logo.png">
                </inertia-link>

                <nav class="px-4 pb-4 sm:flex sm:p-0">
                    <inertia-link class="block px-4 py-2 text-white ripple-bg-brand" href="/about">About</inertia-link>
                    <inertia-link class="block px-4 py-2 text-white ripple-bg-brand" href="/contact">Contact
                    </inertia-link>
                </nav>
            </div>

            <div class="flex justify-between items-center px-4 py-3 sm:p-0">
                <inertia-link class="block px-4 py-2 text-white ripple-bg-brand" href="/home" v-if="auth">
                    {{ auth.name }}
                </inertia-link>
                <template v-else>
                    <inertia-link class="block px-4 py-2 text-white ripple-bg-brand" href="/login">
                        {{ __('Login') }}
                    </inertia-link>

                    <inertia-link class="block px-4 py-2 text-white ripple-bg-brand" href="/register">
                        {{ __('Register') }}
                    </inertia-link>
                </template>
            </div>
        </div>
    </header>
</template>

<script>
export default {
    computed: {
        auth() {
            return this.$page.auth;
        }
    }
}
</script>
