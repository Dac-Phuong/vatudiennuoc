import "./bootstrap";
import "../css/app.css";
import "primeicons/primeicons.css";
import "../assets/styles.scss";

import { createApp, h } from "vue";
import primevue from "./plugins/primevue";
import { createInertiaApp, usePage } from "@inertiajs/vue3";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";

createInertiaApp({
    title: (title) => {
        const page = usePage();
        return title ? `${title}` : page?.props?.settings?.title_web;
    },
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        app.use(plugin).use(ZiggyVue).use(primevue);

        app.mount(el);
        return app;
    },
});
