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
    :export="true"
  />
</template>

<script setup>
onMounted(() => {});

const permissions = {
  create: false,
  read: true,
  update: false,
  delete: true,
};

const title = "Pagos";
const titleBlock = "Lista de Pagos";
const routeFetch = "payments";
const routeName = "payments";
const columns = [
  {
    name: "Preferencia ID",
    field: "preference_id",
    sort: "",
  },
  {
    name: "Estado",
    field: "status",
    sort: "",
  },
  {
    name: "Método",
    field: "payment_method",
    sort: "",
  },
  {
    name: "Tipo",
    field: "payment_type",
    sort: "",
  },
  {
    name: "Monto",
    field: "amount",
    sort: "",
  },
  {
    name: "Estudiante",
    field: "student_name",
    sort: "",
  },
  {
    name: "Grado/Grupo",
    field: "student_grade_group",
    sort: "",
  },
  {
    name: "Matrícula",
    field: "student_enrollment",
    sort: "",
  },
  {
    name: "Fotografía",
    field: "student_photo",
    sort: "",
  },
  {
    name: "Fecha",
    field: "created_at",
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
  name: "",
  last_name: "",
  date_birth: "",
  sex: "",
  email: "",
  phone: "",
  address: "",
  specialty_id: "",
};
const formSchema = [
  {
    type: "input",
    inputType: "text",
    label: "Nombres",
    model: "name",
    class: "col-md-12",
  },
  {
    type: "input",
    inputType: "text",
    label: "Apellidos",
    model: "last_name",
    class: "col-md-12",
  },
  {
    type: "select",
    label: "Especialidad",
    model: "specialty_id",
    class: "col-md-12",
    labelApi: "description",
    values: [],
  },
];
</script>
