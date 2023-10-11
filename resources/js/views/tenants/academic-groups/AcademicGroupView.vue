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
onMounted(async () => {
	await getGrades();
	await getSections();
	await getSchoolCycles();
});

const getGrades = async () => {
	const { data } = await api.get(`/lists/grades`);
	formSchema[0].values = data;
};

const getSections = async () => {
	const { data } = await api.get(`/lists/sections`);
	formSchema[1].values = data;
};

const getSchoolCycles = async () => {
	const { data } = await api.get(`/lists/school-cycles`);
	formSchema[2].values = data;
};

const permissions = {
	create: true,
	read: true,
	update: true,
	delete: true,
};

const title = "Grupos Académicos";
const titleBlock = "Lista de Grupos Académicos";
const routeFetch = "academic-groups";
const routeName = "academic-groups";
const columns = [
	{
		name: "Grado",
		field: "grade",
		sort: "",
	},
	{
		name: "Sección",
		field: "section",
		sort: "",
	},
	{
		name: "Año Escolar",
		field: "school_year",
		sort: "",
	},
	{
		name: "Límite",
		field: "student_limit",
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
	grade_id: "",
	section_id: "",
	school_cycle_id: "",
	student_limit: "",
};
const formSchema = [
	{
		type: "select",
		label: "Grado",
		model: "grade_id",
		class: "col-md-6",
		labelApi: "description",
		values: [],
	},
	{
		type: "select",
		label: "Sección",
		model: "section_id",
		class: "col-md-6",
		labelApi: "description",
		values: [],
	},
	{
		type: "select",
		label: "Año Escolar",
		model: "school_cycle_id",
		class: "col-md-6",
		labelApi: "description",
		values: [],
	},
	{
		type: "input",
		inputType: "number",
		label: "Límite Estudiantes",
		model: "student_limit",
		class: "col-md-6",
	},
];
</script>
