import { createRouter, createWebHashHistory } from "vue-router";
import { useCentralStore } from "@/stores/central";

import NProgress from "nprogress/nprogress.js";

// Main layout variations
import LayoutSimple from "@/layouts/variations/Simple.vue";
import LayoutLanding from "@/layouts/variations/Landing.vue";
import LayoutBackend from "@/layouts/variations/BackendStarter.vue";

const Landing = () => import("@/views/central/LandingView.vue");
const Dashboard = () => import("@/views/central/DashboardView.vue");
const Login = () => import("@/views/central/LoginView.vue");
const Tenant = () => import("@/views/central/tenants/TenantView.vue");

// Errors
const Error400 = () => import("@/views/errors/400View.vue");
const Error401 = () => import("@/views/errors/401View.vue");
const Error403 = () => import("@/views/errors/403View.vue");
const Error404 = () => import("@/views/errors/404View.vue");
const Error500 = () => import("@/views/errors/500View.vue");
const Error503 = () => import("@/views/errors/503View.vue");

const requireAuth = async (to, from, next) => {
  const centralStore = useCentralStore();
  const isAuth = centralStore.isLoggedIn;

  if (!isAuth) {
    next({ name: "login" });
    return;
  }
  next();
};

// Set all routes
const routes = [
  {
    path: "/",
    component: LayoutLanding,
    children: [
      {
        path: "",
        name: "landing",
        component: Landing,
      },
    ],
  },
  {
    path: "/",
    component: LayoutSimple,
    children: [
      {
        path: "login",
        name: "login",
        component: Login,
      },
    ],
  },
  {
    path: "/",
    component: LayoutBackend,
    children: [
      {
        path: "dashboard",
        name: "dashboard",
        component: Dashboard,
        beforeEnter: requireAuth,
      },
      {
        path: "tenants",
        name: "tenants",
        component: Tenant,
        beforeEnter: requireAuth,
      },
    ],
  },
  /*
  |
  |--------------------------------------------------------------------------
  | Error Routes
  |--------------------------------------------------------------------------
  |
  */
  {
    path: "/errors",
    component: LayoutSimple,
    children: [
      {
        path: "400",
        name: "error-400",
        component: Error400,
      },
      {
        path: "401",
        name: "error-401",
        component: Error401,
      },
      {
        path: "403",
        name: "error-403",
        component: Error403,
      },
      {
        path: "404",
        name: "error-404",
        component: Error404,
      },
      {
        path: "500",
        name: "error-500",
        component: Error500,
      },
      {
        path: "503",
        name: "error-503",
        component: Error503,
      },
    ],
  },
];

// Create Router
const router = createRouter({
  history: createWebHashHistory(),
  linkActiveClass: "active",
  linkExactActiveClass: "",
  scrollBehavior() {
    return { left: 0, top: 0 };
  },
  routes,
});

// NProgress
/*eslint-disable no-unused-vars*/
NProgress.configure({ showSpinner: false });

router.beforeResolve((to, from, next) => {
  if (to.name) {
    NProgress.start();
  }

  next();
});

router.afterEach(() => {
  NProgress.done();
});
/*eslint-enable no-unused-vars*/

export default router;
