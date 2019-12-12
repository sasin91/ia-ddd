<template>
    <div class="bg-white shadow-md rounded">
        <div class="text-2xl text-brand">
            {{ ageGroup.name }}
            <small class="text-gray-500">{{ number }}</small>

            <font-awesome-icon icon="expand" @click="$emit('editPassenger', { ageGroup, number })" class="float-right mt-2" />
        </div>
    </div>
</template>

<script>
    export default {
        name: "DockedPassenger",

        props: {
            number: Number,
            ageGroup: Object
        }
    }
</script>