export default {
    computed: {
        translations() {
            if (this.$page) {
                return this.$page.translations
            }

            if (this.$parent) {
                return this.$parent.props.translations
            }

            return []
        }
    },

    methods: {
        /**
         * Translate the given key.
         */
        __ (key, replace) {
            let translation = this.translations[key] || key;

            if (typeof replace === 'object') {
                Object.keys(replace).forEach(key => translation = translation.replace(`:${key}`, replace[key]));
            }

            if (Array.isArray(replace)) {
                replace.forEach((value, key) => translation = translation.replace(`:${key}`, value));
            }

            return translation
        }
    }
}
