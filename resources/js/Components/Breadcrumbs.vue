<template>
    <nav class="my-8 w-full border-b border-brand text-gray-500" aria-label="Breadcrumb">
        <slot tag="ol" :classes="listClasses">
            <ol :class="listClasses">
                <li class="flex items-center">
                    <slot name="home-link">
                        <inertia-link href="/">
                            <font-awesome-icon icon="home" size="2x" class="text-brand" />
                            <font-awesome-icon icon="chevron-right" size="2x" />
                        </inertia-link>
                    </slot>
                </li>

                <slot name="links" :URLFragments="URLFragments" classes="flex items-center">
                    <li v-if="URLFragments.length > 0" v-for="(fragment, i) in URLFragments" :key="i" class="flex items-center">
                        <slot name="link" :fragment="fragment">
                            <inertia-link :href="fragment" class="text-brand font-bold">
                                {{ fragment }}
                            </inertia-link>
                            <font-awesome-icon icon="chevron-right" size="2x" />
                        </slot>
                    </li>
                </slot>
            </ol>
        </slot>
    </nav>
</template>

<script>
    export default {
        name: "Breadcrumbs",

        computed: {
            URLFragments() {
                return this.$inertia.page.url.split('/').filter(String)
            },

            listClasses() {
                return "list-none p-0 inline-flex"
            }
        }
    }
</script>