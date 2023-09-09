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
  formSchema[3].values = data;
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
    name: "Apellidos",
    field: "last_name",
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
    label: "Sexo",
    model: "sex",
    class: "col-md-12",
    labelApi: "description",
    values: [
      {
        id: "MASCULINO",
        description: "MASCULINO",
      },
      {
        id: "FEMENINO",
        description: "FEMENINO",
      },
    ],
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
