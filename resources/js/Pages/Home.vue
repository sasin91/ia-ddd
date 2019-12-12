<template>
    <div class="flex">
        <div class="w-full bg-blue-300 h-12">
            <h1>{{ auth }}</h1>
        </div>
        <div class="w-1/6 bg-gray-500 h-12"></div>
        <div class="w-1/6 bg-gray-400 h-12"></div>
        <div class="w-1/6 bg-gray-500 h-12"></div>
        <div class="w-1/6 bg-gray-400 h-12"></div>
        <div class="w-1/6 bg-gray-500 h-12"></div>
    </div>
</template>

<script>
    import Layout from "~/Shared/Layout";

    export default {
        layout: Layout,

        name: "Home",

        components: {Layout},

        computed: {
            auth() {
                return this.$page.auth;
            }
        }
    }
</script>