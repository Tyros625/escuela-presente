<template>
  <Index3View
    :title="title"
    :title-block="titleBlock"
    :fields-search="fieldsForSearch"
    :route-fetch="routeFetch"
    :route-name="routeName"
    :columns="columns"
    :form-schema="formSchema"
    :form-model="formModel"
    :permissions="permissions"
  />
</template>

<script setup>
import { computed } from "vue";

const permissions = {
  create: true,
  read: true,
  update: true,
  delete: true,
};

const title = "Clientes";
const titleBlock = "Lista de Clientes";
const routeFetch = "tenants";
const routeName = "tenants";
const columns = [
  {
    name: "CCT",
    field: "cct",
    sort: "",
  },
  {
    name: "Nombre",
    field: "school_name",
    sort: "",
  },
  {
    name: "Dominio",
    field: "id",
    sort: "",
  },
  {
    name: "Email",
    field: "email",
    sort: "",
  },
  {
    name: "Creado",
    field: "created_at",
    sort: "",
  },
];
const fieldsForSearch = computed(() =>
  columns.filter((a) => a.field != "action").map((a) => a.field)
);
const formModel = {
  domain: "",
  email: "",
  password: "",
  password_confirmation: "",
  school_name: "",
  cct: "",
  country_code: 52,
  phone: "9876543",
};
const formSchema = [
  {
    type: "input",
    inputType: "text",
    label: "CCT",
    model: "cct",
    class: "col-md-12",
  },
  {
    type: "input",
    inputType: "text",
    label: "Nombre Escuela",
    model: "school_name",
    class: "col-md-12",
  },
  {
    type: "input",
    inputType: "text",
    label: "Nombre Dominio",
    model: "domain",
    class: "col-md-12",
  },
  {
    type: "input",
    inputType: "email",
    label: "Correo Electrónico",
    model: "email",
    class: "col-md-12",
  },
  {
    type: "input",
    inputType: "password",
    label: "Contraseña",
    model: "password",
    class: "col-md-12",
  },
  {
    type: "input",
    inputType: "password",
    label: "Confirmar Contraseña",
    model: "password_confirmation",
    class: "col-md-12",
  },
];
</script>
