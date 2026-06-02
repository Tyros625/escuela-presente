import { createApp } from "vue";
import { createPinia } from "pinia";
import App from "./App.vue";
import { useTemplateStore } from "@/stores/template";
import { initThemeFromStorage } from "@/services/themePreference";

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

// Log errors in console to debug white screen
app.config.errorHandler = (err, instance, info) => {
  console.error("Vue Error:", err);
  console.error("Info:", info);
};

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
    let user, permissions;
    try {
      user = JSON.parse(localStorage.getItem("user") || "null");
      permissions = JSON.parse(localStorage.getItem("permissions") || "[]");
    } catch (_) {
      return;
    }
    if (!user || !user.role) return;
    const { value } = binding;
    if (user.role === "Super Admin" || (Array.isArray(permissions) && permissions.includes(value))) {
      return;
    }
    el.parentNode && el.parentNode.removeChild(el);
  },
});

// Use Pinia and Vue Router
app.use(createPinia());
initThemeFromStorage(useTemplateStore());

// Prefer server-set tenant flag so tenant menu shows even when URL is not a subdomain
const useTenantApp = window.__TENANT_APP === true || isSubdomain();
if (useTenantApp) {
  app.use(tenantRoutes);
  window.__ESCUELA_TENANT_APP = true; // tenant app with role-based menu (for deploy verification)
} else {
  app.use(centralRoutes);
}

const SPLASH_MIN_VISIBLE_MS = 1000;

function dismissAppSplash() {
  const splash = document.getElementById("app-splash");
  if (!splash) return;

  const shownAt = window.__APP_SPLASH_AT ?? Date.now();
  const waitMs = Math.max(0, SPLASH_MIN_VISIBLE_MS - (Date.now() - shownAt));

  const runDismiss = () => {
    splash.style.transition = "opacity 0.5s ease, visibility 0.5s ease";
    splash.style.opacity = "0";
    splash.style.visibility = "hidden";
    splash.setAttribute("aria-busy", "false");
    const remove = () => splash.remove();
    splash.addEventListener("transitionend", remove, { once: true });
    window.setTimeout(remove, 600);
  };

  if (waitMs > 0) {
    window.setTimeout(runDismiss, waitMs);
  } else {
    runDismiss();
  }
}

// ..and finally mount it!
app.mount("#app");
requestAnimationFrame(() => requestAnimationFrame(dismissAppSplash));
