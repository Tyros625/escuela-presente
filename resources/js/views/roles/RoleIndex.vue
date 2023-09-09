<template>
  <IndexView
    :title="title"
    :title-block="titleBlock"
    :fields-search="fieldsForSearch"
    :route-fetch="routeFetch"
    :route-name="routeName"
    :columns="columns"
    :permissions="permissions"
  />
</template>

<script setup>
import { useUserStore } from "@/stores/user";

const userStore = useUserStore();
const permissions = {
  create: userStore.userCan("create role"),
  read: userStore.userCan("read role"),
  update: userStore.userCan("update role"),
  delete: userStore.userCan("delete role"),
};

const title = "Roles";
const titleBlock = "Lista de Roles";
const routeFetch = "roles";
const routeName = "roles";
const columns = [
  {
    name: "Nombre",
    field: "name",
    sort: "",
  },
  {
    name: "Acciones",
    field: "action",
    sort: "",
  },
];
const fieldsForSearch = computed(() =>
  columns.filter((a) => a.field != "action").map((a) => a.field)
);
</script>
