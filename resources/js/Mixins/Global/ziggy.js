/**
 * Adds the route method to all components
 */
export default {
    methods: {
        route (name, params) {
            return window.route(name, params)
        }
    }
}
