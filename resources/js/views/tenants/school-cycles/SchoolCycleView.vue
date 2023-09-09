<template>
  <IndexTenantView
    :title="title"
    :title-block="titleBlock"
    :fields-search="fieldsForSearch"
    :route-fetch="routeFetch"
    :route-name="routeName"
    :columns="columns"
    :form-schema="formSchema"
    :form-model="formModel"
    :permissions="permissions"
    :import="false"
  />
</template>

<script setup>
onMounted(() => {});

const permissions = {
  create: true,
  read: true,
  update: true,
  delete: true,
};

const title = "Años Escolares";
const titleBlock = "Lista de Años Escolares";
const routeFetch = "school-cycles";
const routeName = "school-cycles";
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
