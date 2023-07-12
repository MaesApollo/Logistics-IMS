import Vue from "vue";
import { BootstrapVue, IconsPlugin } from "bootstrap-vue";
import VueResource from "vue-resource";
import VeeValidate from "vee-validate";
import App from "./App.vue";
import Router from "./routes.js";
import Auth from "./packages/auth/Auth.js";
import Global from "./packages/local/Global.js";
import VueBarcode from "@chenfengyuan/vue-barcode";
import VueSimpleAccordion from "vue-simple-accordion";
import DateRangePicker from "vue-mj-daterangepicker";
import "vue-simple-accordion/dist/vue-simple-accordion.css";
import fullCalendar from "vue-fullcalendar";
import PortalVue from "portal-vue";
import { BTable } from "bootstrap-vue";
import VueEnglishdatepicker from "vue-englishdatepicker";
import VueHtmlToPaper from "vue-html-to-paper";
import { BTabs } from "bootstrap-vue";
import { TabsPlugin } from "bootstrap-vue";
import { BModal } from "bootstrap-vue";
import { CollapsePlugin } from "bootstrap-vue";
import PrettyCheckbox from "pretty-checkbox-vue";

import datePicker from "vue-bootstrap-datetimepicker";
import "pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css";
import "bootstrap/dist/css/bootstrap.css";
import "bootstrap-vue/dist/bootstrap-vue.css";
import "vue-search-select/dist/VueSearchSelect.css";
import "vue-mj-daterangepicker/dist/vue-mj-daterangepicker.css";

import "./plugins/node-waves/waves.js";
import "./plugins/jquery/jquery.js";
import "./plugins/bootstrap/js/bootstrap.js";
import "./plugins/jquery-slimscroll/jquery.slimscroll.js";
import "./plugins/bootstrap-notify/bootstrap-notify.js";

import "./js/admin.js";
import "./js/pages/ui/notifications.js";

import "./js/demo.js";

const popcorn = document.querySelector("#popcorn");
const tooltip = document.querySelector("#tooltip");
const options = {
    name: "_blank",
    specs: ["fullscreen=yes", "titlebar=yes", "scrollbars=yes"],
    styles: [
        "https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css",
        "https://unpkg.com/kidlat-css/css/kidlat.css"
    ]
};

Vue.component("full-calendar", fullCalendar);
Vue.use(VueSimpleAccordion);
Vue.use(PrettyCheckbox);
Vue.use(VueResource);
Vue.use(BootstrapVue);
Vue.use(IconsPlugin);
Vue.use(Auth);
Vue.use(Global);
Vue.use(datePicker);
Vue.use(PortalVue);
Vue.use(VueEnglishdatepicker);
Vue.use(require("vue-moment"));
Vue.use(CollapsePlugin);
Vue.component("b-modal", BModal);
Vue.component("b-table", BTable);
Vue.use(VueHtmlToPaper, options);
Vue.component("b-tabs", BTabs);
Vue.use(TabsPlugin);
Vue.use(DateRangePicker);
Vue.component(VueBarcode.name, VueBarcode);
Vue.use(VeeValidate, {
    fieldsBagName: "veeFields"
});
console.log(window.location.host);

if (window.location.host == "ims.dctechmicro.com") {
    Vue.http.options.root = "http://ims.dctechmicro.com/back/";
    Vue.prototype.$img_path =
        "http://ims.dctechmicro.com/back/public/attachments/";
    Vue.prototype.$back_url = "http://ims.dctechmicro.com/back/";
} else if (window.location.host == "localhost:8080") {
    Vue.http.options.root = "http://localhost:8000";
    // Vue.prototype.$img_path = "http://localhost:8000/imgs/";
    Vue.prototype.$img_path = "http://localhost:8000/attachments/";
    Vue.prototype.$back_url = "http://localhost:8000/back/";
    // Vue.prototype.$attachment_path = "http://localhost:8000/attachments/";
} else if (window.location.host == "120.72.31.54") {
    // Vue.prototype.$url_back = protocol + "//120.72.31.54/back/";
    // Vue.http.options.root = protocol + "//120.72.31.54/back/";

    Vue.http.options.root = "http://120.72.31.54/back/";
    Vue.prototype.$img_path = "http://120.72.31.54/back/public/attachments/";
    Vue.prototype.$back_url = "http://120.72.31.54/back/";
}
Vue.prototype.$AppliedDateoptions = {
    format: "YYYY-MM-DD",
    useCurrent: false
};
Vue.http.headers.common["Authorization"] = "Bearer " + Vue.auth.getToken();

// fetch("imgPath.txt")
//   .then(response => response.text())
//   .then(text => (Vue.prototype.$img_path = text));

Router.beforeEach((to, from, next) => {
    if (to.matched.some(record => record.meta.forVisitors)) {
        if (Vue.auth.isAuthenticated()) {
            next({
                path: "/home"
            });
        } else next();
    } else if (to.matched.some(record => record.meta.forAuth)) {
        if (!Vue.auth.isAuthenticated()) {
            next({
                path: "/login"
            });
        } else next();
    } else next();
});

new Vue({
    render: h => h(App),
    router: Router
}).$mount("#app");