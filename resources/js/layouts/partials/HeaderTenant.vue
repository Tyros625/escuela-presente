<template>
  <!-- Header -->
  <header id="page-header">
    <slot>
      <!-- Header Content -->
      <div class="content-header">
        <slot name="content">
          <!-- Left Section -->
          <div class="d-flex align-items-center">
            <slot name="content-left">
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
            </slot>
          </div>
          <!-- END Left Section -->

          <!-- Right Section -->
          <div class="d-flex align-items-center">
            <slot name="content-right">
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
                  <div
                    class="p-3 text-center bg-body-light border-bottom rounded-top"
                  >
                    <img
                      class="img-avatar img-avatar48 img-avatar-thumb"
                      src="/assets/media/avatars/avatar10.jpg"
                      alt="Header Avatar"
                    />
                    <p class="mt-2 mb-0 fw-medium">
                      {{ userStore.getUser.name }}
                    </p>
                    <p class="mb-0 text-muted fs-sm fw-medium">
                      {{ userStore.getUser.roles[0]?.name }}
                    </p>
                  </div>
                  <div class="p-2">
                    <RouterLink
                      :to="{ name: 'general-config' }"
                      class="dropdown-item d-flex align-items-center justify-content-between"
                    >
                      <span class="fs-sm fw-medium"> Configuración </span>
                    </RouterLink>
                  </div>
                  <div class="p-2">
                    <a
                      class="dropdown-item d-flex align-items-center justify-content-between"
                      @click="modalShow(`modal-password`)"
                    >
                      <span class="fs-sm fw-medium"> Cambiar Contraseña </span>
                    </a>
                  </div>
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
            </slot>
          </div>
          <!-- END Right Section -->
        </slot>
      </div>
      <!-- END Header Content -->

      <!-- Header Loader -->
      <div
        id="page-header-loader"
        class="overlay-header bg-body-extra-light"
        :class="{ show: store.settings.headerLoader }"
      >
        <div class="content-header">
          <div class="w-100 text-center">
            <i class="fa fa-fw fa-circle-notch fa-spin"></i>
          </div>
        </div>
      </div>
      <!-- END Header Loader -->
    </slot>
  </header>
  <!-- END Header -->
  <ModalChangePassword />
</template>

<script setup>
import { useTemplateStore } from "@/stores/template";
import { useUserStore } from "@/stores/user";

onMounted(() => {});

// Main store and Router
const store = useTemplateStore();
const router = useRouter();
const userStore = useUserStore();

function logout() {
  userStore.logout();
  router.push({ name: "auth-signin" });
}

// When ESCAPE key is hit close the header search section
function eventHeaderSearch(event) {
  if (event.which === 27) {
    event.preventDefault();
    store.headerSearch({ mode: "off" });
  }
}

// Attach ESCAPE key event listener
onMounted(() => {
  document.addEventListener("keydown", eventHeaderSearch);
});

// Remove keydown event listener
onUnmounted(() => {
  document.removeEventListener("keydown", eventHeaderSearch);
});

function modalShow(modalName) {
  var myModal = new bootstrap.Modal(document.getElementById(modalName));
  myModal.show();
}
</script>
