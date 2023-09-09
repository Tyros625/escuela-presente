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
      <div class="content-header">
        <slot name="header">
          <!-- Logo -->
          <RouterLink :to="{ name: 'landing' }" class="fw-semibold text-dual">
            <span class="smini-visible">
              <i class="fa fa-circle-notch text-primary"></i>
            </span>
            <span class="smini-hide fs-5 tracking-wider">
              Escuela<span class="fw-normal">Presente</span>
            </span>
          </RouterLink>
          <!-- END Logo -->
        </slot>

        <!-- Extra -->
        <div>
          <!-- Close Sidebar, Visible only on mobile screens -->
          <button
            type="button"
            class="d-lg-none btn btn-sm btn-alt-secondary ms-1"
            @click="store.sidebar({ mode: 'close' })"
          >
            <i class="fa fa-fw fa-times"></i>
          </button>
          <!-- END Close Sidebar -->
        </div>
        <!-- END Extra -->
      </div>
      <!-- END Side Header -->

      <!-- Sidebar Scrolling -->
      <div id="simplebar-sidebar" class="js-sidebar-scroll">
        <slot name="content">
          <!-- Side Navigation -->
          <div class="content-side">
            <MenuView />
            <!-- <MenuView v-if="userStore.isSuperAdmin" />
            <MenuUserView v-else /> -->
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

// SimpleBar, for more info and examples you can check out https://github.com/Grsmto/simplebar/tree/master/packages/simplebar-vue
import SimpleBar from "simplebar";

// Main store
const store = useTemplateStore();
const userStore = useUserStore();

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
