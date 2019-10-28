require('./bootstrap')

import { InertiaApp } from '@inertiajs/inertia-vue'

/**
 * Add the __() method to all components for easy translations inside components.
 */
import Translatable from './Mixins/Global/translatable'

import Vue from 'vue'

Vue.use(InertiaApp)
Vue.mixin(Translatable)

let app = document.getElementById('app')

new Vue({
  render: h => h(InertiaApp, {
    props: {
      initialPage: JSON.parse(app.dataset.page),
      resolveComponent: (name) => {
        return import(`@/Pages/${name}`).then(module => module.default)
      },
    },
  }),
}).$mount(app)
