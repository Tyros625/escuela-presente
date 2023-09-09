<script setup>
import { useTemplateStore } from "@/stores/template";
import BaseLayout from "@/layouts/BaseLayout.vue";
import BaseNavigation from "@/components/BaseNavigation.vue";
import { useUserStore } from "@/stores/user";

const userStore = useUserStore();
const router = useRouter();
// Main store
const store = useTemplateStore();

// Set default elements for this layout
store.setLayout({
  header: true,
  sidebar: true,
  sideOverlay: true,
  footer: true,
});

// Set various template options for this layout variation
store.headerStyle({ mode: "light" });
store.mainContent({ mode: "narrow" });

function logout() {
  userStore.logout();
  router.push({ name: "login" });
}
</script>

<template>
  <BaseLayout>
    <!-- Side Overlay Content -->
    <!-- Using the available v-slot, we can override the default Side Overlay content from layouts/partials/SideOvelay.vue -->
    <template #side-overlay-content>
      <div class="content-side">
        <p>Side Overlay content..</p>
      </div>
    </template>
    <!-- END Side Overlay Content -->

    <!-- Sidebar Content -->
    <!-- Using the available v-slot, we can override the default Sidebar content from layouts/partials/Sidebar.vue -->
    <template #sidebar-content>
      <div class="content-side">
        <BaseNavigation
          :nodes="[
            {
              name: 'Dashboard',
              to: 'dashboard',
              icon: 'si si-speedometer',
            },
            {
              name: 'Clientes',
              to: 'tenants',
              icon: 'si si-users',
            },
            {
              name: 'Más',
              heading: true,
            },
            {
              name: 'Landing',
              to: 'landing',
              icon: 'si si-rocket',
            },
          ]"
        />
      </div>
    </template>
    <!-- END Sidebar Content -->

    <!-- Header Content Left -->
    <!-- Using the available v-slot, we can override the default Header content from layouts/partials/Header.vue -->
    <template #header-content-left>
      <!-- Toggle Sidebar -->
      <button
        type="button"
        class="btn btn-sm btn-alt-secondary me-2 d-lg-none"
        @click="store.sidebar({ mode: 'toggle' })"
      >
        <i class="fa fa-fw fa-bars"></i>
      </button>
      <!-- END Toggle Sidebar -->

      <!-- Toggle Mini Sidebar -->
      <button
        type="button"
        class="btn btn-sm btn-alt-secondary me-2 d-none d-lg-inline-block"
        @click="store.sidebarMini({ mode: 'toggle' })"
      >
        <i class="fa fa-fw fa-ellipsis-v"></i>
      </button>
      <!-- END Toggle Mini Sidebar -->
    </template>
    <!-- END Header Content Left -->

    <!-- Header Content Right -->
    <!-- Using the available v-slot, we can override the default Header content from layouts/partials/Header.vue -->
    <template #header-content-right>
      <!-- User Dropdown -->
      <div class="dropdown d-inline-block ms-2">
        <button
          type="button"
          class="btn btn-sm btn-alt-secondary d-flex align-items-center"
          id="page-header-user-dropdown"
          data-bs-toggle="dropdown"
          aria-haspopup="true"
          aria-expanded="false"
        >
          <img
            class="rounded-circle"
            src="/assets/media/avatars/avatar10.jpg"
            alt="Header Avatar"
            style="width: 21px"
          />
          <span class="d-none d-sm-inline-block ms-2">
            {{ userStore.getUser.name }}
          </span>
          <i
            class="fa fa-fw fa-angle-down d-none d-sm-inline-block opacity-50 ms-1 mt-1"
          ></i>
        </button>
        <div
          class="dropdown-menu dropdown-menu-md dropdown-menu-end p-0 border-0"
          aria-labelledby="page-header-user-dropdown"
        >
          <div class="p-3 text-center bg-body-light border-bottom rounded-top">
            <img
              class="img-avatar img-avatar48 img-avatar-thumb"
              src="/assets/media/avatars/avatar10.jpg"
              alt="Header Avatar"
            />
            <p class="mt-2 mb-0 fw-medium">
              {{ userStore.getUser.name }}
            </p>
          </div>
          <!-- <div class="p-2">
            <RouterLink
              :to="{ name: 'profile' }"
              class="dropdown-item d-flex align-items-center justify-content-between"
            >
              <span class="fs-sm fw-medium">
                Perfil
              </span>
            </RouterLink>
            <RouterLink
              :to="{ name: 'general-config' }"
              class="dropdown-item d-flex align-items-center justify-content-between"
            >
              <span class="fs-sm fw-medium">
                Configuración
              </span>
            </RouterLink>
          </div> -->
          <div role="separator" class="dropdown-divider m-0"></div>
          <div class="p-2">
            <a
              class="dropdown-item d-flex align-items-center justify-content-between"
              @click="logout"
            >
              <span class="fs-sm fw-medium"> Cerrar Sesión </span>
            </a>
          </div>
        </div>
      </div>
      <!-- END User Dropdown -->
      <!-- Toggle Side Overlay -->
      <button
        type="button"
        class="btn btn-sm btn-alt-secondary ms-2"
        @click="store.sideOverlay({ mode: 'toggle' })"
      >
        <i class="fa fa-fw fa-list-ul fa-flip-horizontal"></i>
      </button>
      <!-- END Toggle Side Overlay -->
    </template>
    <!-- END Header Content Right -->

    <!-- Footer Content Left -->
    <!-- Using the available v-slot, we can override the default Footer content from layouts/partials/Footer.vue -->
    <template #footer-content-left>
      <strong>My App</strong>
      &copy; {{ store.app.copyright }}
    </template>
    <!-- END Footer Content Left -->
  </BaseLayout>
</template>
