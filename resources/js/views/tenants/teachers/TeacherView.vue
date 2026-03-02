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
    :import="true"
  />
</template>

<script setup>
onMounted(() => {
  getSpecialties();
});

const getSpecialties = async () => {
  const { data } = await api.get(`/lists/specialties`);
  formSchema.find((f) => f.model === "specialty_id").values = data;
};

const permissions = {
  create: true,
  read: true,
  update: true,
  delete: true,
};

const title = "Profesores";
const titleBlock = "Lista de Profesores";
const routeFetch = "teachers";
const routeName = "teachers";
const columns = [
  {
    name: "Nombres",
    field: "name",
    sort: "",
  },
  {
    name: "Apellido Paterno",
    field: "last_name_father",
    sort: "",
  },
  {
    name: "Apellido Materno",
    field: "last_name_mother",
    sort: "",
  },
  {
    name: "RFC",
    field: "rfc",
    sort: "",
  },
  {
    name: "Especialidad",
    field: "specialty",
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
  last_name_father: "",
  last_name_mother: "",
  rfc: "",
  specialty_id: "",
  max_hours_per_week: "",
  available_hours: "",
  institutional_email: "",
};
const formSchema = [
  {
    type: "input",
    inputType: "text",
    label: "Nombres",
    model: "name",
    class: "col-md-6",
  },
  {
    type: "input",
    inputType: "text",
    label: "Apellido Paterno",
    model: "last_name_father",
    class: "col-md-6",
  },
  {
    type: "input",
    inputType: "text",
    label: "Apellido Materno",
    model: "last_name_mother",
    class: "col-md-6",
  },
  {
    type: "input",
    inputType: "text",
    label: "RFC",
    model: "rfc",
    class: "col-md-6",
  },
  {
    type: "select",
    label: "Especialidad",
    model: "specialty_id",
    class: "col-md-6",
    labelApi: "description",
    values: [],
  },
  {
    type: "input",
    inputType: "number",
    label: "Horas máximas por semana",
    model: "max_hours_per_week",
    class: "col-md-6",
  },
  {
    type: "input",
    inputType: "text",
    label: "Horarios disponibles",
    model: "available_hours",
    class: "col-md-6",
  },
  {
    type: "input",
    inputType: "email",
    label: "Correo institucional",
    model: "institutional_email",
    class: "col-md-6",
  },
];
</script>
