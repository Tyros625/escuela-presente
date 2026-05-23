<template>
  <!-- Sidebar -->
  <!--
    Sidebar Mini Mode - Display Helper classes

    Adding 'smini-hide' class to an element will make it invisible (opacity: 0) when the sidebar is in mini mode
    Adding 'smini-show' class to an element will make it visible (opacity: 1) when the sidebar is in mini mode
    If you would like to disable the transition animation, make sure to also add the 'no-transition' class to your element

    Adding 'smini-hidden' to an element will hide it when the sidebar is in mini mode
    Adding 'smini-visible' to an element will show it (display: inline-block) only when the sidebar is in mini mode
    Adding 'smini-visible-block' to an element will show it (display: block) only when the sidebar is in mini mode
  -->
  <nav id="sidebar" aria-label="Main Navigation">
    <slot>
      <!-- Side Header -->
      <div class="content-header ep-sidebar-header">
        <slot name="header">
          <!-- Logo -->
          <RouterLink :to="{ name: 'landing' }" class="ep-sidebar-brand smini-hide">
            Escuela<span class="ep-sidebar-brand-light">Presente</span>
          </RouterLink>
          <RouterLink :to="{ name: 'landing' }" class="ep-sidebar-brand-mini smini-visible">
            <span class="ep-sidebar-brand-mark">EP</span>
          </RouterLink>
        </slot>

        <div class="ep-sidebar-header-actions">
          <button
            type="button"
            class="ep-sidebar-collapse d-none d-lg-inline-flex"
            @click="store.sidebarMini({ mode: 'toggle' })"
            :title="store.settings.sidebarMini ? 'Expandir menú' : 'Contraer menú'"
            aria-label="Contraer menú"
          >
            <i
              class="fa-solid fa-chevron-left ep-sidebar-collapse-icon"
              :class="{ 'is-collapsed': store.settings.sidebarMini }"
            ></i>
          </button>
          <button
            type="button"
            class="ep-sidebar-close d-lg-none"
            @click="store.sidebar({ mode: 'close' })"
            aria-label="Cerrar menú"
          >
            <i class="fa fa-fw fa-xmark"></i>
          </button>
        </div>
      </div>
      <!-- END Side Header -->

      <!-- User info (wireframe) -->
      <div v-if="userStore.isLoggedIn" class="content-side-content ep-sidebar-user px-3 py-3 border-bottom">
        <div class="ep-sidebar-user-name smini-hide">{{ userDisplayName }}</div>
        <span class="ep-role-badge smini-hide mt-2">{{ roleLabel }}</span>
      </div>
      <!-- END User info -->

      <!-- Sidebar Scrolling -->
      <div id="simplebar-sidebar" class="js-sidebar-scroll">
        <slot name="content">
          <!-- Side Navigation -->
          <div class="content-side">
            <MenuView />
          </div>
          <!-- END Side Navigation -->
        </slot>
      </div>
      <!-- END Sidebar Scrolling -->
    </slot>
  </nav>
  <!-- END Sidebar -->
</template>

<script setup>
import MenuView from "@/components/MenuView.vue";
import { useTemplateStore } from "@/stores/template";
import { useUserStore } from "@/stores/user";
import { computed } from "vue";

// SimpleBar, for more info and examples you can check out https://github.com/Grsmto/simplebar/tree/master/packages/simplebar-vue
import SimpleBar from "simplebar";

// Main store
const store = useTemplateStore();
const userStore = useUserStore();

const userDisplayName = computed(() => {
  const u = userStore.getUser;
  if (!u) return "";
  return u.name || u.first_name || u.email || "Usuario";
});

const roleLabel = computed(() => {
  const r = userStore.getUser?.role;
  if (!r) return "Usuario";
  const map = {
    Administrador: "Administrador",
    Admin: "Administrador",
    "Super Admin": "Administrador",
    Docente: "Docente",
    Estudiante: "Estudiante",
    "Padre/Tutor": "Padre/Tutor",
    Usuario: "Usuario",
  };
  return map[r] || r;
});

// Dark Mode preference helper for radios
const radioDarkMode = ref();

// Sets default dark mode preferences for radios
function setDarkModeRadioDefault() {
  if (store.settings.darkModeSystem) {
    radioDarkMode.value = "system";
  } else {
    if (store.settings.darkMode) {
      radioDarkMode.value = "dark";
    } else {
      radioDarkMode.value = "light";
    }
  }
}

// When the user sets dark mode preference through the radios
function onDarkModeRadioChange() {
  if (radioDarkMode.value === "system") {
    store.darkModeSystem({ mode: "on" });
  } else {
    store.darkModeSystem({ mode: "off" });

    if (radioDarkMode.value === "dark") {
      store.darkMode({ mode: "on" });
    } else {
      store.darkMode({ mode: "off" });
    }
  }
}

// Set dark mode preference radios default and watch for changes to store
setDarkModeRadioDefault();
watch(
  () => store.settings.darkModeSystem,
  () => {
    setDarkModeRadioDefault();
  }
);
watch(
  () => store.settings.darkMode,
  () => {
    setDarkModeRadioDefault();
  }
);

// Init SimpleBar (custom scrolling)
onMounted(() => {
  new SimpleBar(document.getElementById("simplebar-sidebar"));
});
</script>
