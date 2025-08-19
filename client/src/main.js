import { createApp } from "vue";
import { createPinia } from "pinia";
import App from "./App.vue";
import Toast, { POSITION, TYPE } from "vue-toastification";
import "vue-toastification/dist/index.css";

// Import Bootstrap JS
import "bootstrap/dist/js/bootstrap.bundle.min.js";
// Import global SCSS (includes Bootstrap CSS)
import "./assets/scss/main.scss";
import "./assets/theme.scss";

import router from "./router";


const options = {
    position: POSITION.TOP_RIGHT,
    type: TYPE.SUCCESS || TYPE.ERROR || TYPE.INFO || TYPE.WARNING,

};

// Create and mount the app
const app = createApp(App);
app.use(createPinia());
app.use(router);
app.use(Toast, options);


app.mount("#app");
