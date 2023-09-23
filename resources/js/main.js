import { createApp } from "vue";
import { createPinia } from "pinia";
import App from "./App.vue";

// You can use the following starter router instead of the default one as a clean starting point
import tenantRoutes from "./router/tenant";
import centralRoutes from "./router/central";
import { isSubdomain } from "@/services/host";

// Template components
import BaseBlock from "@/components/BaseBlock.vue";
import BaseBackground from "@/components/BaseBackground.vue";
import BasePageHeading from "@/components/BasePageHeading.vue";

// Template directives
import clickRipple from "@/directives/clickRipple";

// Bootstrap framework
import * as bootstrap from "bootstrap";
window.bootstrap = bootstrap;

// Craft new application
const app = createApp(App);

// Register global components
app.component("BaseBlock", BaseBlock);
app.component("BaseBackground", BaseBackground);
app.component("BasePageHeading", BasePageHeading);

// Register global directives
app.directive("click-ripple", clickRipple);
app.directive("uppercase", (el) => {
  el.value = el.value.toUpperCase();
});
app.directive("user-can", {
  mounted(el, binding) {
    const user = JSON.parse(localStorage.getItem("user"));
    const permissions = JSON.parse(localStorage.getItem("permissions"));
    const { value } = binding;

    if (user.role === "Super Admin" || permissions.includes(value)) {
      return;
    }

    el.parentNode && el.parentNode.removeChild(el);
  },
});

// Use Pinia and Vue Router
app.use(createPinia());

if (isSubdomain()) {
  app.use(tenantRoutes);
} else {
  app.use(centralRoutes);
}

// ..and finally mount it!
app.mount("#app");
