<template>
    <transition :name="transition">
        <div
            v-if="visible"
            class="fixed inset-0 w-full h-screen flex items-center justify-center bg-semi-75"
            @click.self="close"
        >
            <div class="relative w-full max-w-2xl bg-white shadow-lg rounded-lg p-8">
                <button
                    aria-label="close"
                    class="absolute top-0 right-0 text-xl text-gray-500 my-2 mx-4"
                    @click.prevent="close"
                >
                    ×
                </button>
                <slot />
            </div>
        </div>
    </transition>
</template>

<style scoped>
    @keyframes zoom {
        0% {
            opacity: 0;
            transform: scale3d(.3, .3, .3);
        }

        50% {
            opacity: 1;
        }
    }

    .zoom-enter-active {
        animation: zoom 0.4s ease;
    }

    .zoom-leave-active {
        animation: zoom 0.4s reverse;
    }
</style>

<script>
    export default {
        name: "Modal",

        props: {
            transition: {
                type: String,
                default: 'zoom'
            },

            visible: {
                type: Boolean,
                required: true
            },

            allowBackgroundScrolling: {
                type: Boolean,
                required: false,
                default: false
            }
        },

        watch: {
            visible(value) {
                if (this.allowBackgroundScrolling === false) {
                    if (value) {
                        return document.querySelector('body').classList.add('overflow-hidden');
                    }

                    document.querySelector('body').classList.remove('overflow-hidden');
                }
            }
        },

        methods: {
            close() {
                this.$emit('close')
            }
        }
    }
</script>