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
		name: "Turno",
		field: "shift",
		sort: "",
	},
	{
		name: "Salón / Aula",
		field: "room_name",
		sort: "",
	},
	{
		name: "Límite",
		field: "student_limit",
		sort: "",
	},
	{
		name: "Materias",
		field: "subjects_count",
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
	shift: "morning",
	room_name: "",
	student_limit: "",
	subjects_count: 0,
	subjects: [],
};
const subjectsCountOptions = Array.from({ length: 20 }, (_, i) => ({
	id: i + 1,
	description: String(i + 1),
}));
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
		type: "select",
		label: "Turno",
		model: "shift",
		class: "col-md-6",
		labelApi: "description",
		values: [
			{ id: "morning", description: "Mañana" },
			{ id: "afternoon", description: "Tarde" },
		],
	},
	{
		type: "input",
		inputType: "number",
		label: "Límite Estudiantes",
		model: "student_limit",
		class: "col-md-6",
	},
	{
		type: "input",
		inputType: "text",
		label: "Salón / Aula",
		model: "room_name",
		class: "col-md-6",
	},
	{
		type: "select",
		label: "Número de materias",
		model: "subjects_count",
		class: "col-md-6",
		labelApi: "description",
		values: subjectsCountOptions,
	},
	{
		type: "subjects",
		label: "Materias",
		model: "subjects",
		countModel: "subjects_count",
		itemPlaceholder: "Materia",
		class: "col-md-12",
	},
];
</script>
