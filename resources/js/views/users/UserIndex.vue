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
  create: userStore.userCan("create user"),
  read: userStore.userCan("read user"),
  update: userStore.userCan("update user"),
  delete: userStore.userCan("delete user"),
};

const title = "Usuarios";
const titleBlock = "Lista de Usuarios";
const routeFetch = "users";
const routeName = "users";
const columns = [
  {
    name: "Rol",
    field: "role",
    sort: "",
  },
  {
    name: "Nombre",
    field: "name",
    sort: "",
  },
  {
    name: "Correo Electrónico",
    field: "email",
    sort: "",
  },
  {
    name: "Activo",
    field: "active",
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
