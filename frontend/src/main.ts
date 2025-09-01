/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * BublFiz Social
 * Description: Vue 3 application entry point with PrimeVue and Bootstrap setup
 * Location: /frontend/src/main.ts
 */

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import PrimeVue from 'primevue/config'
import Aura from '@primevue/themes/aura'

// Bootstrap CSS
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap/dist/js/bootstrap.bundle.min.js'

// PrimeVue components (import as needed)
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Dropdown from 'primevue/dropdown'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Dialog from 'primevue/dialog'
import Toolbar from 'primevue/toolbar'
import FileUpload from 'primevue/fileupload'
import Timeline from 'primevue/timeline'
import Rating from 'primevue/rating'
import SpeedDial from 'primevue/speeddial'

import App from './App.vue'
import router from './router'

const app = createApp(App)

// Configure PrimeVue with Aura theme
app.use(PrimeVue, {
  theme: {
    preset: Aura,
    options: {
      prefix: 'p',
      darkModeSelector: '.dark-mode',
      cssLayer: false
    }
  }
})

// Register global PrimeVue components
app.component('Button', Button)
app.component('InputText', InputText)
app.component('Dropdown', Dropdown)
app.component('DataTable', DataTable)
app.component('Column', Column)
app.component('Dialog', Dialog)
app.component('Toolbar', Toolbar)
app.component('FileUpload', FileUpload)
app.component('Timeline', Timeline)
app.component('Rating', Rating)
app.component('SpeedDial', SpeedDial)

app.use(createPinia())
app.use(router)

app.mount('#app')