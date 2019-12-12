<template>
    <button
        @click="$emit('click', $event)"
        class="font-semibold py-2 px-4 bg-white hover:bg-gray-100 text-brand"
        :class="stateClasses"
    >
        <slot></slot>
    </button>
</template>

<script>
    export default {
        name: "TravelType",

        props: {
            isSelected: Boolean
        },

        computed: {
            stateClasses() {
                if (this.isSelected) {
                    return "shadow border border-brand"
                }
            }
        }
    }
</script>