<template>
  <IndexView :title="title" :title-block="titleBlock" :fields-search="fieldsForSearch" :route-fetch="routeFetch"
    :route-name="routeName" :columns="columns" :form-schema="formSchema" :form-model="formModel"
    :permissions="permissions" />
</template>

<script setup>
import IndexView from "@/components/IndexTenantView.vue";

const permissions = {
  create: true,
  read: true,
  update: true,
  delete: true,
};

const title = "Incidentes";
const titleBlock = "Lista de Incidentes";
const routeFetch = "incidents";
const routeName = "incidents";
const columns = [
  {
    name: "Descripción",
    field: "description",
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
const formModel = {
  description: "",
};
const formSchema = [
  {
    type: "input",
    inputType: "text",
    label: "Descripción",
    model: "description",
    class: "col-md-12",
  },
];
</script>
