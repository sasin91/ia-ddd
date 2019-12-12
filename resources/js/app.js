require('./bootstrap')

import { InertiaApp } from '@inertiajs/inertia-vue'
import Vue from 'vue'

Vue.use(InertiaApp)

const mixins = require.context('./Mixins/Global/', true, /\.js$/i);
mixins.keys().forEach(key => Vue.mixin(mixins(key).default));

const plugins = require.context('./Plugins/', true, /\.js$/i);
plugins.keys().forEach(key => require('./Plugins/'+key.split('./').pop()));

let app = document.getElementById('app')

new Vue({
  render: h => h(InertiaApp, {
    props: {
      initialPage: JSON.parse(app.dataset.page),
      resolveComponent: (name) => {
        return import(`~/Pages/${name}`).then(module => module.default)
      },
    },
  }),
}).$mount(app)
