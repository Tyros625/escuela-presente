<template>
  <header id="page-header">
    <slot>
      <div class="content-header">
        <slot name="content">
          <div class="d-flex align-items-center">
            <slot name="content-left">
              <button
                type="button"
                class="ep-icon-btn me-2 d-lg-none"
                @click="store.sidebar({ mode: 'toggle' })"
                aria-label="Abrir menú"
              >
                <i class="fa fa-fw fa-bars"></i>
              </button>
            </slot>
          </div>

          <div class="d-flex align-items-center ms-auto">
            <slot name="content-right">
              <div class="dropdown d-inline-block">
                <button
                  type="button"
                  class="ep-user-chip"
                  id="page-header-user-dropdown"
                  data-bs-toggle="dropdown"
                  aria-haspopup="true"
                  aria-expanded="false"
                >
                  <span class="ep-user-avatar">{{ userInitials }}</span>
                  <span class="d-none d-sm-inline-block">{{ userDisplayName }}</span>
                  <i class="fa fa-fw fa-chevron-down opacity-50 fs-xs"></i>
                </button>
                <div
                  class="dropdown-menu dropdown-menu-end p-2"
                  aria-labelledby="page-header-user-dropdown"
                  style="min-width: 220px"
                >
                  <div class="px-2 py-2 mb-1">
                    <div class="fw-semibold text-dark">{{ userDisplayName }}</div>
                    <div class="text-muted fs-sm">{{ userStore.getUser?.role || "—" }}</div>
                  </div>
                  <div class="dropdown-divider my-1"></div>
                  <RouterLink :to="{ name: 'general-config' }" class="dropdown-item">
                    Configuración
                  </RouterLink>
                  <button type="button" class="dropdown-item" @click="modalShow('modal-password')">
                    Cambiar contraseña
                  </button>
                  <div class="dropdown-divider my-1"></div>
                  <button type="button" class="dropdown-item text-danger" @click="logout">
                    Cerrar sesión
                  </button>
                </div>
              </div>
            </slot>
          </div>
        </slot>
      </div>

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
    </slot>
  </header>
  <ModalChangePassword />
</template>

<script setup>
import { useTemplateStore } from "@/stores/template";
import { useUserStore } from "@/stores/user";

const store = useTemplateStore();
const router = useRouter();
const userStore = useUserStore();

const userDisplayName = computed(() => {
  const u = userStore.getUser;
  if (!u) return "Usuario";
  return u.name || u.first_name || u.email || "Usuario";
});

const userInitials = computed(() => {
  const name = userDisplayName.value || "";
  const parts = name.trim().split(/\s+/).filter(Boolean);
  if (!parts.length) return "U";
  if (parts.length === 1) return parts[0].charAt(0).toUpperCase();
  return (parts[0].charAt(0) + parts[parts.length - 1].charAt(0)).toUpperCase();
});

function logout() {
  userStore.logout();
  router.push({ name: "auth-signin" });
}

function eventHeaderSearch(event) {
  if (event.which === 27) {
    event.preventDefault();
    store.headerSearch({ mode: "off" });
  }
}

onMounted(() => {
  document.addEventListener("keydown", eventHeaderSearch);
});

onUnmounted(() => {
  document.removeEventListener("keydown", eventHeaderSearch);
});

function modalShow(modalName) {
  bootstrap.Modal.getOrCreateInstance(document.getElementById(modalName)).show();
}
</script>
