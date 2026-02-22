<template>
  <BaseNavigationTenant :nodes="menuNodes" />
</template>

<script setup>
import BaseNavigationTenant from "@/components/BaseNavigationTenant.vue";
import { getMenuNodesForRole } from "@/config/menuByRole";
import { useUserStore } from "@/stores/user";
import { computed } from "vue";

const userStore = useUserStore();

const menuNodes = computed(() => {
  const role = userStore.getUser?.role;
  return getMenuNodesForRole(role, userStore);
});
</script>
