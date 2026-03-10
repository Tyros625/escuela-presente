<template>
	<BasePageHeading title="Gestión de Grupos">
		<template #extra>
			<div class="d-flex gap-2 align-items-center flex-wrap">
				<select class="form-select form-select-sm w-auto" v-model="selectedSchoolCycleId">
					<option :value="null">Todos los ciclos</option>
					<option v-for="c in schoolCycles" :key="c.id" :value="c.id">
						{{ c.description }}
					</option>
				</select>

				<button type="button" class="btn btn-sm btn-alt-primary" disabled>
					<i class="fa-solid fa-wand-magic-sparkles me-1"></i>
					Asignación Auto
				</button>

				<button type="button" class="btn btn-sm btn-primary" @click="openCreateModal">
					<i class="fa fa-plus opacity-50 me-1"></i>
					Nuevo Grupo
				</button>
			</div>
		</template>
	</BasePageHeading>

	<div class="content">
		<BaseBlock title="Grupos Registrados" content-full>
			<div class="table-responsive">
				<table class="table table-striped table-hover mb-0 align-middle">
					<thead>
						<tr class="text-uppercase fs-xs">
							<th>Grupo</th>
							<th>Grado</th>
							<th>Turno</th>
							<th class="text-center">Alumnos</th>
							<th class="text-center">Materias</th>
							<th style="min-width: 160px">Cobertura</th>
							<th>Salón</th>
							<th class="text-end">Acciones</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="g in filteredGroups" :key="g.id">
							<td class="fw-semibold">
								{{ g.name || g.group_label }}
							</td>
							<td>{{ g.grade }}</td>
							<td>
								<span
									:class="[
										'badge rounded-pill',
										g.shift === 'afternoon' ? 'bg-warning text-dark' : 'bg-info',
									]"
								>
									{{ g.shift === "afternoon" ? "Vespertino" : "Matutino" }}
								</span>
							</td>
							<td class="text-center">{{ g.students_count ?? 0 }}</td>
							<td class="text-center">{{ g.subjects_count ?? 0 }}</td>
							<td>
								<div class="d-flex align-items-center gap-2">
									<span class="fs-xs fw-semibold" :class="coverageTextClass(g.coverage_percent)">
										{{ Math.min(100, Number(g.coverage_percent || 0)) }}%
									</span>
									<div class="progress flex-grow-1" style="height: 8px">
										<div
											class="progress-bar"
											:class="coverageBarClass(g.coverage_percent)"
											role="progressbar"
											:style="{
												width: `${Math.min(100, Number(g.coverage_percent || 0))}%`,
											}"
										/>
									</div>
								</div>
							</td>
							<td class="text-muted">{{ g.room_name || "-" }}</td>
							<td class="text-end">
								<div class="btn-group">
									<button type="button" class="btn btn-sm btn-alt-secondary" disabled>
										<i class="fa-regular fa-calendar me-1"></i>
										Horario
									</button>
									<button type="button" class="btn btn-sm btn-alt-warning" @click="openEditModal(g)">
										<i class="fa-solid fa-pen"></i>
									</button>
									<button type="button" class="btn btn-sm btn-alt-danger" @click="confirmDelete(g)">
										<i class="fa-solid fa-trash"></i>
									</button>
								</div>
							</td>
						</tr>
						<tr v-if="!filteredGroups.length">
							<td colspan="8" class="text-center text-muted py-4">Sin grupos</td>
						</tr>
					</tbody>
				</table>
			</div>
		</BaseBlock>
	</div>

	<div
		class="modal fade"
		id="modal-academic-groups"
		tabindex="-1"
		aria-labelledby="modal-academic-groups-label"
		aria-hidden="true"
		data-bs-backdrop="static"
	>
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modal-academic-groups-label">
						{{ modalType === "add" ? "Nuevo Grupo" : "Editar Grupo" }}
					</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<form @submit.prevent="onSubmit">
						<div class="row g-3">
							<div class="col-md-6">
								<label class="form-label">Nombre del Grupo</label>
								<input
									class="form-control"
									type="text"
									v-model="form.name"
									placeholder="Ej. 1°A"
									v-uppercase
									:disabled="isSaving"
								/>
							</div>
							<div class="col-md-6">
								<label class="form-label">Grado</label>
								<select class="form-select" v-model="form.grade_id" :disabled="isSaving">
									<option :value="''">Elige un valor...</option>
									<option v-for="g in grades" :key="g.id" :value="g.id">
										{{ g.description }}
									</option>
								</select>
							</div>

							<div class="col-md-6">
								<label class="form-label">Sección</label>
								<select class="form-select" v-model="form.section_id" :disabled="isSaving">
									<option :value="''">Elige un valor...</option>
									<option v-for="s in sections" :key="s.id" :value="s.id">
										{{ s.description }}
									</option>
								</select>
							</div>
							<div class="col-md-6">
								<label class="form-label">Año Escolar</label>
								<select class="form-select" v-model="form.school_cycle_id" :disabled="isSaving">
									<option :value="''">Elige un valor...</option>
									<option v-for="c in schoolCycles" :key="c.id" :value="c.id">
										{{ c.description }}
									</option>
								</select>
							</div>

							<div class="col-md-6">
								<label class="form-label">Work shift</label>
								<select class="form-select" v-model="form.shift" :disabled="isSaving">
									<option value="morning">morning</option>
									<option value="afternoon">afternoon</option>
								</select>
							</div>
							<div class="col-md-6">
								<label class="form-label">Capacidad máx. alumnos</label>
								<input
									class="form-control"
									type="number"
									min="1"
									step="1"
									v-model="form.student_limit"
									:disabled="isSaving"
								/>
							</div>

							<div class="col-md-12">
								<label class="form-label">Room Name</label>
								<input
									class="form-control"
									type="text"
									v-model="form.room_name"
									placeholder="Ej. Aula 12-B"
									v-uppercase
									:disabled="isSaving"
								/>
							</div>

							<div class="col-md-6">
								<label class="form-label">Number of subjects</label>
								<select class="form-select" v-model.number="form.subjects_count" :disabled="isSaving">
									<option v-for="n in 20" :key="n" :value="n">{{ n }}</option>
								</select>
							</div>

							<div class="col-md-12">
								<label class="form-label">Subjects</label>
								<div class="row g-2">
									<div class="col-md-6" v-for="i in form.subjects_count" :key="i">
										<input
											class="form-control"
											type="text"
											v-model="form.subjects[i - 1]"
											:placeholder="`Subject ${i}`"
											v-uppercase
											:disabled="isSaving"
										/>
									</div>
								</div>
							</div>

							<div class="col-12 text-end mt-2">
								<button type="button" class="btn btn-alt-secondary me-2" data-bs-dismiss="modal">
									Cancelar
								</button>
								<button type="submit" class="btn btn-primary" :disabled="isSaving">
									<i class="fa fa-cog fa-spin" v-if="isSaving"></i>
									<i class="fa-solid fa-floppy-disk" v-else></i>
									{{ modalType === "add" ? "Guardar Grupo" : "Actualizar Grupo" }}
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
import api from "@/services/api";
import Swal from "sweetalert2";

const groups = ref([]);
const grades = ref([]);
const sections = ref([]);
const schoolCycles = ref([]);
const selectedSchoolCycleId = ref(null);

const modalType = ref("add");
const rowSelected = ref(null);
const isSaving = ref(false);

const form = reactive({
	name: "",
	grade_id: "",
	section_id: "",
	school_cycle_id: "",
	shift: "morning",
	student_limit: 40,
	room_name: "",
	subjects_count: 8,
	subjects: [],
});

const filteredGroups = computed(() => {
	if (!selectedSchoolCycleId.value) return groups.value;
	return groups.value.filter((g) => g.school_cycle_id === selectedSchoolCycleId.value);
});

function coverageBarClass(p) {
	const n = Number(p || 0);
	if (n >= 90) return "bg-success";
	if (n >= 70) return "bg-warning";
	return "bg-danger";
}

function coverageTextClass(p) {
	const n = Number(p || 0);
	if (n >= 90) return "text-success";
	if (n >= 70) return "text-warning";
	return "text-danger";
}

function normalizeSubjects() {
	const count = Math.max(1, Math.min(20, Number(form.subjects_count || 1)));
	if (!Array.isArray(form.subjects)) form.subjects = [];
	if (form.subjects.length > count) form.subjects = form.subjects.slice(0, count);
	while (form.subjects.length < count) form.subjects.push("");
}

watch(
	() => form.subjects_count,
	() => normalizeSubjects(),
	{ immediate: true }
);

async function fetchLists() {
	const [g, s, c] = await Promise.all([
		api.get(`/lists/grades`),
		api.get(`/lists/sections`),
		api.get(`/lists/school-cycles`),
	]);
	grades.value = g.data;
	sections.value = s.data;
	schoolCycles.value = c.data;
}

async function fetchGroups() {
	const { data } = await api.get(`/academic-groups`);
	groups.value = data.data;
}

function resetForm() {
	Object.assign(form, {
		name: "",
		grade_id: "",
		section_id: "",
		school_cycle_id: selectedSchoolCycleId.value || "",
		shift: "morning",
		student_limit: 40,
		room_name: "",
		subjects_count: 8,
		subjects: [],
	});
	normalizeSubjects();
}

function openCreateModal() {
	modalType.value = "add";
	rowSelected.value = null;
	resetForm();
	showModal();
}

function openEditModal(g) {
	modalType.value = "edit";
	rowSelected.value = g;
	Object.assign(form, {
		name: g.name || "",
		grade_id: g.grade_id,
		section_id: g.section_id,
		school_cycle_id: g.school_cycle_id,
		shift: g.shift || "morning",
		student_limit: g.student_limit || 40,
		room_name: g.room_name || "",
		subjects_count: Array.isArray(g.subjects) ? g.subjects.length : Number(g.subjects_count || 8),
		subjects: Array.isArray(g.subjects) ? [...g.subjects] : [],
	});
	normalizeSubjects();
	showModal();
}

function showModal() {
	const el = document.getElementById("modal-academic-groups");
	const m = new bootstrap.Modal(el);
	m.show();
}

function hideModal() {
	const el = document.getElementById("modal-academic-groups");
	const m = bootstrap.Modal.getOrCreateInstance(el);
	m.hide();
}

async function onSubmit() {
	isSaving.value = true;
	try {
		const payload = {
			name: form.name,
			grade_id: form.grade_id,
			section_id: form.section_id,
			school_cycle_id: form.school_cycle_id,
			shift: form.shift,
			room_name: form.room_name,
			student_limit: form.student_limit,
			subjects: form.subjects,
		};
		if (modalType.value === "add") {
			await api.post(`/academic-groups`, payload);
		} else {
			await api.put(`/academic-groups/${rowSelected.value.id}`, payload);
		}
		await fetchGroups();
		hideModal();
	} finally {
		isSaving.value = false;
	}
}

function confirmDelete(g) {
	Swal.fire({
		title: "¿Eliminar grupo?",
		text: g.name || g.group_label,
		showCancelButton: true,
		confirmButtonText: "Si",
		cancelButtonText: "Cancelar",
	}).then(async (result) => {
		if (!result.isConfirmed) return;
		await api.delete(`/academic-groups/${g.id}`);
		await fetchGroups();
	});
}

onMounted(async () => {
	await fetchLists();
	await fetchGroups();
	if (schoolCycles.value.length && selectedSchoolCycleId.value == null) {
		selectedSchoolCycleId.value = schoolCycles.value[0].id;
	}
});
</script>
