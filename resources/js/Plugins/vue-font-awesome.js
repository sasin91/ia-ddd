import Vue from 'vue'
import { library } from '@fortawesome/fontawesome-svg-core'
import {FontAwesomeIcon, FontAwesomeLayers} from '@fortawesome/vue-fontawesome'
import {
    faArrowLeft,
    faArrowRight,
    faCalendar,
    faChevronDown, faChevronRight, faExpand, faHome,
    faMapMarker,
    faPlaneDeparture, faSpinner
} from "@fortawesome/free-solid-svg-icons";
import {faExchange} from "@fortawesome/pro-solid-svg-icons";
import {faExchange as fadExchange} from "@fortawesome/pro-duotone-svg-icons"

/**
 * Imports the font awesome icons as SVG components
 * @see https://github.com/FortAwesome/vue-fontawesome
 */
library.add(faMapMarker, faChevronDown, faCalendar, faPlaneDeparture, faArrowLeft, faArrowRight, faExchange, fadExchange, faChevronRight, faHome, faPlaneDeparture, faExpand, faSpinner)
Vue.component('font-awesome-icon', FontAwesomeIcon)
Vue.component('font-awesome-layers', FontAwesomeLayers)