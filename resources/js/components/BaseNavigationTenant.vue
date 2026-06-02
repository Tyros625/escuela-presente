<script setup>
import { computed, onBeforeUnmount, onMounted, reactive, ref } from "vue";
import { useRoute } from "vue-router";
import { useTemplateStore } from "@/stores/template";
import BaseNavigationTenant from "@/components/BaseNavigationTenant.vue";

const store = useTemplateStore();
const route = useRoute();

const props = defineProps({
  nodes: {
    type: Array,
    description: "The nodes of the navigation",
  },
  subMenu: {
    type: Boolean,
    default: false,
  },
  dark: {
    type: Boolean,
    default: false,
  },
  horizontal: {
    type: Boolean,
    default: false,
  },
  horizontalHover: {
    type: Boolean,
    default: false,
  },
  horizontalCenter: {
    type: Boolean,
    default: false,
  },
  horizontalJustify: {
    type: Boolean,
    default: false,
  },
  disableClick: {
    type: Boolean,
    default: false,
  },
});

const windowWidth = ref(typeof window !== "undefined" ? window.innerWidth : 1200);

const isMiniNav = computed(
  () => windowWidth.value >= 992 && store.settings.sidebarMini
);

const flyout = reactive({
  visible: false,
  title: "",
  items: [],
  top: 0,
  left: 0,
});

const flyoutStyle = computed(() => ({
  top: `${flyout.top}px`,
  left: `${flyout.left}px`,
}));

function isSidebarMiniDesktop() {
  return isMiniNav.value;
}

function visibleSubItems(node) {
  return (node.sub || []).filter((entry) => entry.attributes?.show !== false);
}

function closeFlyout() {
  flyout.visible = false;
  flyout.title = "";
  flyout.items = [];
}

function openFlyout(triggerEl, node) {
  closeFlyout();

  const rect = triggerEl.getBoundingClientRect();
  const menuWidth = 260;
  let left = rect.right + 8;
  const top = Math.max(8, Math.min(rect.top, window.innerHeight - 80));

  if (left + menuWidth > window.innerWidth - 8) {
    left = Math.max(8, rect.left - menuWidth - 8);
  }

  flyout.title = node.name;
  flyout.items = visibleSubItems(node);
  flyout.top = top;
  flyout.left = left;
  flyout.visible = true;
}

function subIsActive(paths) {
  const activePaths = Array.isArray(paths) ? paths : [paths];
  return activePaths.some((path) => route.path.indexOf(path) === 0);
}

const classContainer = computed(() => ({
  "nav-main": !props.subMenu,
  "nav-main-submenu": props.subMenu,
  "nav-main-dark": props.dark,
  "nav-main-horizontal": props.horizontal,
  "nav-main-hover": props.horizontalHover,
  "nav-main-horizontal-center": props.horizontalCenter,
  "nav-main-horizontal-justify": props.horizontalJustify,
}));

function linkClicked(e, submenu, node) {
  if (submenu && isSidebarMiniDesktop()) {
    e.preventDefault();
    e.stopPropagation();

    if (flyout.visible && flyout.title === node.name) {
      closeFlyout();
      return;
    }

    openFlyout(e.currentTarget, node);
    return;
  }

  if (submenu) {
    const el = e.target.closest("li");

    if (
      !(
        window.innerWidth > 991 &&
        ((props.horizontal && props.horizontalHover) || props.disableClick)
      )
    ) {
      if (el.classList.contains("open")) {
        el.classList.remove("open");
      } else {
        Array.from(el.closest("ul").children).forEach((element) => {
          element.classList.remove("open");
        });
        el.classList.add("open");
      }
    }
    return;
  }

  if (window.innerWidth < 992) {
    store.sidebar({ mode: "close" });
  }

  if (isSidebarMiniDesktop()) {
    closeFlyout();
  }
}

function onFlyoutLinkClick() {
  closeFlyout();
  if (window.innerWidth < 992) {
    store.sidebar({ mode: "close" });
  }
}

function onDocumentClick(event) {
  const panel = document.querySelector(".ep-nav-mini-flyout-panel");
  if (panel?.contains(event.target)) {
    return;
  }

  if (isSidebarMiniDesktop()) {
    closeFlyout();
  }
}

function onResize() {
  windowWidth.value = window.innerWidth;
  if (!isSidebarMiniDesktop()) {
    closeFlyout();
  }
}

onMounted(() => {
  document.addEventListener("click", onDocumentClick);
  window.addEventListener("resize", onResize);
});

onBeforeUnmount(() => {
  document.removeEventListener("click", onDocumentClick);
  window.removeEventListener("resize", onResize);
});
</script>

<template>
  <ul :class="classContainer">
    <template v-for="(node, index) in nodes" :key="`node-${index}`">
      <li
        v-if="node.attributes.show"
        :class="{
          'nav-main-heading': node.heading,
          'nav-main-item': !node.heading,
          open:
            !isMiniNav &&
            node.sub &&
            node.subActivePaths &&
            subIsActive(node.subActivePaths),
        }"
      >
        {{ node.heading ? node.name : "" }}

        <div v-if="!node.heading && !node.sub" class="ep-nav-item-wrap" @click="linkClicked($event)">
          <RouterLink
            :to="node.to && node.to !== '#' ? { name: node.to } : '#'"
            class="nav-main-link"
            :title="node.name"
            :active-class="node.to && node.to !== '#' ? 'active' : ''"
          >
            <i v-if="node.icon" :class="`nav-main-link-icon ${node.icon}`"></i>
            <span v-if="node.name && !subMenu" class="nav-main-link-name ep-nav-top-label">
              {{ node.name }}
            </span>
            <span
              v-if="node.badge"
              class="nav-main-link-badge badge rounded-pill ep-nav-top-label"
              :class="node['badge-variant'] ? `bg-${node['badge-variant']}` : 'bg-primary'"
            >
              {{ node.badge }}
            </span>
          </RouterLink>
        </div>

        <a
          v-else-if="!node.heading && node.sub"
          href="#"
          class="nav-main-link nav-main-link-submenu"
          :title="node.name"
          @click.prevent="linkClicked($event, true, node)"
        >
          <i v-if="node.icon" :class="`nav-main-link-icon ${node.icon}`"></i>
          <span v-if="node.name && !subMenu" class="nav-main-link-name ep-nav-top-label">
            {{ node.name }}
          </span>
          <span
            v-if="node.badge"
            class="nav-main-link-badge badge rounded-pill ep-nav-top-label"
            :class="node['badge-variant'] ? `bg-${node['badge-variant']}` : 'bg-primary'"
          >
            {{ node.badge }}
          </span>
        </a>

        <BaseNavigationTenant
          v-if="node.sub && !isMiniNav"
          :nodes="node.sub"
          sub-menu
          :disable-click="props.horizontal && props.horizontalHover"
        />
      </li>
    </template>
  </ul>

  <Teleport v-if="!subMenu" to="body">
    <div
      v-if="flyout.visible && isMiniNav"
      class="ep-nav-mini-flyout-panel"
      :style="flyoutStyle"
      role="menu"
      :aria-label="flyout.title"
      @click.stop
    >
      <div class="ep-nav-mini-flyout-panel__title">{{ flyout.title }}</div>
      <RouterLink
        v-for="(entry, idx) in flyout.items"
        :key="`flyout-${idx}-${entry.to}`"
        :to="entry.to && entry.to !== '#' ? { name: entry.to } : '#'"
        class="ep-nav-mini-flyout-panel__link"
        role="menuitem"
        @click="onFlyoutLinkClick"
      >
        {{ entry.name }}
      </RouterLink>
      <div v-if="!flyout.items.length" class="ep-nav-mini-flyout-panel__empty text-muted">
        Sin opciones
      </div>
    </div>
  </Teleport>
</template>
