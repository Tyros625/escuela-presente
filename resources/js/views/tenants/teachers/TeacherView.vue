<template>
  <BasePageHeading :title="title">
    <template #extra>
      <div class="d-flex gap-2 align-items-center">
        <button
          v-if="showImport"
          type="button"
          class="btn btn-sm btn-outline-success"
          @click="modalShow('modal-import')"
        >
          <i class="fa-solid fa-file-excel me-1"></i>
          Importar
        </button>
        <button
          v-if="permissions.create"
          type="button"
          class="btn btn-primary"
          @click="modalShow('modal-teachers')"
        >
          <i class="fa fa-plus me-1"></i>
          Nuevo Docente
        </button>
      </div>
    </template>
  </BasePageHeading>

  <div class="content">
    <BaseBlock content-full>
      <!-- Toolbar -->
      <div class="d-flex flex-column flex-md-row gap-3 mb-4">
        <div class="flex-grow-1">
          <div class="input-group">
            <span class="input-group-text bg-body border-end-0">
              <i class="fa-solid fa-magnifying-glass text-muted"></i>
            </span>
            <input
              v-model="searchQuery"
              type="text"
              class="form-control border-start-0"
              placeholder="Buscar docente..."
            />
          </div>
        </div>
        <div class="d-flex gap-2 align-items-center">
          <select
            v-model="filterTurno"
            class="form-select form-select-sm"
            style="min-width: 140px"
          >
            <option value="">Todos los turnos</option>
            <option value="Matutino">Matutino</option>
            <option value="Vespertino">Vespertino</option>
            <option value="Ambos">Ambos</option>
          </select>
        </div>
      </div>

      <!-- Catalog Header -->
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 mb-4">
        <div class="d-flex align-items-center gap-2">
          <div class="teacher-catalog-icon">
            <i class="fa-solid fa-chalkboard-user"></i>
          </div>
          <h4 class="mb-0 fw-semibold">Catálogo de Docentes</h4>
        </div>
        <span class="text-muted fs-sm">{{ filteredTeachers.length }} registros</span>
      </div>

      <!-- Table -->
      <div class="table-responsive">
        <table class="table table-hover align-middle teacher-catalog-table">
          <thead>
            <tr>
              <th class="text-uppercase fs-xs fw-semibold text-muted">Docente</th>
              <th class="text-uppercase fs-xs fw-semibold text-muted">RFC / ID</th>
              <th class="text-uppercase fs-xs fw-semibold text-muted">Especialidad</th>
              <th class="text-uppercase fs-xs fw-semibold text-muted">Turno</th>
              <th class="text-uppercase fs-xs fw-semibold text-muted">Horas Disp.</th>
              <th class="text-uppercase fs-xs fw-semibold text-muted">Horas Asig.</th>
              <th class="text-uppercase fs-xs fw-semibold text-muted">Estado</th>
              <th class="text-uppercase fs-xs fw-semibold text-muted text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="row in paginatedTeachers" :key="row.id">
              <!-- DOCENTE -->
              <td>
                <div class="d-flex align-items-center gap-3">
                  <div
                    class="teacher-avatar"
                    :style="{ backgroundColor: getAvatarColor(row.display_name) }"
                  >
                    {{ getInitials(row) }}
                  </div>
                  <div>
                    <div class="fw-semibold">{{ row.display_name }}</div>
                    <div class="text-muted fs-sm">
                      {{ row.institutional_email || row.email || '-' }}
                    </div>
                  </div>
                </div>
              </td>
              <!-- RFC -->
              <td>
                <span class="font-monospace">{{ row.rfc || '-' }}</span>
              </td>
              <!-- ESPECIALIDAD -->
              <td>
                <span
                  v-if="row.specialty"
                  class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2"
                >
                  {{ row.specialty }}
                </span>
                <span v-else class="text-muted">-</span>
              </td>
              <!-- TURNO -->
              <td>
                <span
                  v-if="row.available_hours"
                  class="badge bg-dark-subtle text-dark rounded-pill px-3 py-2"
                >
                  {{ row.available_hours }}
                </span>
                <span v-else class="text-muted">-</span>
              </td>
              <!-- HORAS DISP. -->
              <td>
                <span v-if="row.max_hours_per_week">
                  {{ row.max_hours_per_week }}h
                </span>
                <span v-else class="text-muted">-</span>
              </td>
              <!-- HORAS ASIG. -->
              <td>
                <span class="text-muted">-</span>
              </td>
              <!-- ESTADO -->
              <td>
                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2">
                  Activo
                </span>
              </td>
              <!-- ACCIONES -->
              <td class="text-end">
                <div class="btn-group btn-group-sm">
                  <button
                    v-if="permissions.update"
                    type="button"
                    class="btn btn-sm btn-outline-secondary"
                    @click="modalShow('modal-teachers', row.id)"
                    title="Editar"
                  >
                    <i class="fa-solid fa-pen"></i>
                  </button>
                  <button
                    v-if="permissions.delete"
                    type="button"
                    class="btn btn-sm btn-outline-danger"
                    @click="destroy(row.id)"
                    title="Eliminar"
                  >
                    <i class="fa-solid fa-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="filteredTeachers.length === 0">
              <td colspan="8" class="text-center py-5 text-muted">
                <i class="fa-solid fa-users fa-2x mb-2 d-block opacity-50"></i>
                No hay docentes registrados
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div
        v-if="totalPages > 1"
        class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top"
      >
        <span class="text-muted fs-sm">
          Mostrando {{ (currentPage - 1) * pageSize + 1 }} -
          {{ Math.min(currentPage * pageSize, filteredTeachers.length) }} de
          {{ filteredTeachers.length }}
        </span>
        <div class="btn-group">
          <button
            class="btn btn-sm btn-outline-secondary"
            :disabled="currentPage <= 1"
            @click="currentPage--"
          >
            <i class="fa-solid fa-chevron-left"></i>
          </button>
          <button
            class="btn btn-sm btn-outline-secondary"
            :disabled="currentPage >= totalPages"
            @click="currentPage++"
          >
            <i class="fa-solid fa-chevron-right"></i>
          </button>
        </div>
      </div>
    </BaseBlock>
  </div>

  <!-- Modal Add/Edit -->
  <div
    class="modal fade"
    id="modal-teachers"
    tabindex="-1"
    aria-labelledby="modal-teachers-label"
    aria-hidden="true"
    data-bs-backdrop="static"
  >
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modal-teachers-label">
            {{ modalType === 'add' ? 'Agregar' : 'Editar' }} Docente
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <ErrorsView v-if="errors.length" :errors="errors" />
          <form @submit.prevent="onSubmit" class="mb-4">
            <div class="row">
              <div
                v-for="(el, index) in formSchema"
                :key="index"
                :class="el.class"
              >
                <label class="form-label">{{ el.label }}</label>
                <input
                  v-if="el.type === 'input'"
                  :type="el.inputType"
                  v-model="form[el.model]"
                  class="form-control"
                  :disabled="isLoading"
                  v-uppercase
                />
                <VueSelect
                  v-else-if="el.type === 'select'"
                  v-model="form[el.model]"
                  :options="el.values"
                  :label="el.labelApi"
                  :reduce="(option) => option.id"
                  placeholder="Elige un valor..."
                  :disabled="isLoading"
                />
              </div>
              <div class="col-md-12 mt-3 text-end">
                <button type="submit" class="btn btn-primary" :disabled="isLoading">
                  <i class="fa fa-cog fa-spin" v-if="isLoading"></i>
                  <i class="fa-solid fa-floppy-disk" v-else></i>
                  {{ modalType === 'add' ? 'Guardar' : 'Actualizar' }}
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Import -->
  <div
    class="modal fade"
    id="modal-import"
    tabindex="-1"
    aria-labelledby="modal-import-label"
    aria-hidden="true"
    data-bs-backdrop="static"
  >
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modal-import-label">Importar Docentes</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <ErrorsView v-if="errors.length" :errors="errors" />
          <form @submit.prevent="importXLS">
            <div class="mb-3">
              <label for="formFile" class="form-label">Seleccione archivo</label>
              <input class="form-control" type="file" id="formFile" @change="onChangeFile" />
            </div>
            <div class="mb-3 text-center">
              <a href="/imports/profesores.xlsx">Descargar Ejemplo</a>
            </div>
            <div class="col-md-12 mt-3 text-end">
              <button type="submit" class="btn btn-primary" :disabled="isLoading">
                <i class="fa fa-cog fa-spin" v-if="isLoading"></i>
                <i class="fa-solid fa-floppy-disk" v-else></i>
                Importar
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import VueSelect from "vue-select";
import api from '@/services/api';

const permissions = {
  create: true,
  read: true,
  update: true,
  delete: true,
};

const title = 'Catálogo de Docentes';
const routeFetch = 'teachers';
const routeName = 'teachers';
const showImport = true;

const formModel = {
  name: '',
  last_name_father: '',
  last_name_mother: '',
  rfc: '',
  specialty_id: '',
  subject_id: '',
  max_hours_per_week: '',
  available_hours: '',
  institutional_email: '',
};

const formSchema = [
  { type: 'input', inputType: 'text', label: 'Nombres', model: 'name', class: 'col-md-6' },
  { type: 'input', inputType: 'text', label: 'Apellido Paterno', model: 'last_name_father', class: 'col-md-6' },
  { type: 'input', inputType: 'text', label: 'Apellido Materno', model: 'last_name_mother', class: 'col-md-6' },
  { type: 'input', inputType: 'text', label: 'RFC', model: 'rfc', class: 'col-md-6' },
  { type: 'select', label: 'Especialidad', model: 'specialty_id', class: 'col-md-6', labelApi: 'description', values: [] },
  { type: 'select', label: 'Subject', model: 'subject_id', class: 'col-md-6', labelApi: 'description', values: [] },
  { type: 'input', inputType: 'number', label: 'Horas máximas por semana', model: 'max_hours_per_week', class: 'col-md-6' },
  { type: 'input', inputType: 'text', label: 'Horarios disponibles', model: 'available_hours', class: 'col-md-6' },
  { type: 'input', inputType: 'email', label: 'Correo institucional', model: 'institutional_email', class: 'col-md-6' },
];

const form = reactive({ ...formModel });
const dataFetched = ref([]);
const errors = ref([]);
const modalType = ref('add');
const rowSelected = ref(null);
const isLoading = ref(false);
const searchQuery = ref('');
const filterTurno = ref('');
const currentPage = ref(1);
const pageSize = 20;
const file = ref(null);

const AVATAR_COLORS = [
  '#3b82f6', '#f59e0b', '#8b5cf6', '#eab308', '#ec4899', '#10b981',
  '#6366f1', '#14b8a6', '#f97316',
];

function getInitials(row) {
  const parts = [row.name, row.last_name_father, row.last_name_mother].filter(Boolean);
  if (parts.length === 0) return '??';
  const first = parts[0].trim().charAt(0)?.toUpperCase() || '';
  const last = parts[parts.length - 1].trim().charAt(0)?.toUpperCase() || '';
  return (first + last).slice(0, 2) || '??';
}

function getAvatarColor(name) {
  if (!name) return AVATAR_COLORS[0];
  let hash = 0;
  for (let i = 0; i < name.length; i++) {
    hash = name.charCodeAt(i) + ((hash << 5) - hash);
  }
  const idx = Math.abs(hash) % AVATAR_COLORS.length;
  return AVATAR_COLORS[idx];
}

const filteredTeachers = computed(() => {
  let list = dataFetched.value;
  const q = searchQuery.value?.toLowerCase().trim() || '';
  const turno = filterTurno.value;

  if (q) {
    list = list.filter(
      (t) =>
        (t.display_name || '').toLowerCase().includes(q) ||
        (t.institutional_email || '').toLowerCase().includes(q) ||
        (t.email || '').toLowerCase().includes(q) ||
        (t.rfc || '').toLowerCase().includes(q) ||
        (t.specialty || '').toLowerCase().includes(q)
    );
  }
  if (turno) {
    list = list.filter((t) => (t.available_hours || '').toLowerCase().includes(turno.toLowerCase()));
  }
  return list;
});

const totalPages = computed(() => Math.ceil(filteredTeachers.value.length / pageSize) || 1);

const paginatedTeachers = computed(() => {
  const start = (currentPage.value - 1) * pageSize;
  return filteredTeachers.value.slice(start, start + pageSize);
});

watch(filteredTeachers, () => {
  if (currentPage.value > totalPages.value) currentPage.value = 1;
});

onMounted(() => {
  getSpecialties();
  getData();
});

const getSpecialties = async () => {
  const { data } = await api.get('/lists/specialties');
  const specialtySchema = formSchema.find((f) => f.model === 'specialty_id');
  const subjectSchema = formSchema.find((f) => f.model === 'subject_id');
  if (specialtySchema) specialtySchema.values = data;
  if (subjectSchema) subjectSchema.values = data;
};

const getData = async () => {
  const { data } = await api.get(`/${routeFetch}`);
  dataFetched.value = data.data || [];
};

function onSubmit() {
  if (modalType.value === 'add') saveData();
  else updateData();
}

function onChangeFile(e) {
  file.value = e.target.files[0];
}

function importXLS() {
  const formData = new FormData();
  formData.append('file', file.value);

  api
    .post(`/${routeName}/import`, formData)
    .then(async () => {
      Toast.fire({ icon: 'success', title: 'Importado correctamente' });
      modalHide('modal-import');
      await getData();
    })
    .catch((err) => {
      errors.value = [];
      Object.getOwnPropertyNames(err.data?.errors || {}).forEach((val) => {
        (err.data.errors[val] || []).forEach((el) => errors.value.push(el));
      });
      Toast.fire({ icon: 'error', title: 'Error al importar' });
    });
}

function saveData() {
  isLoading.value = true;
  api
    .post(`/${routeName}`, form)
    .then(() => {
      isLoading.value = false;
      Object.assign(form, formModel);
      errors.value = [];
      Toast.fire({ icon: 'success', title: 'Guardado correctamente' });
      getData();
      modalHide('modal-teachers');
    })
    .catch((err) => {
      errors.value = [];
      isLoading.value = false;
      Object.getOwnPropertyNames(err.response?.data?.errors || {}).forEach((val) => {
        (err.response.data.errors[val] || []).forEach((el) => errors.value.push(el));
      });
      Toast.fire({ icon: 'error', title: 'Error' });
    });
}

function destroy(id) {
  Swal.fire({
    title: '¿Estás seguro de eliminar?',
    showCancelButton: true,
    confirmButtonText: 'Sí',
    cancelButtonText: 'Cancelar',
  }).then((result) => {
    if (result.isConfirmed) {
      api
        .delete(`/${routeFetch}/${id}`)
        .then(() => {
          Toast.fire({ icon: 'success', title: 'Eliminado correctamente' });
          getData();
        })
        .catch(() => Toast.fire({ icon: 'error', title: 'Error al eliminar' }));
    }
  });
}

function getDataID() {
  api
    .get(`/${routeFetch}/${rowSelected.value}`)
    .then((res) => {
      if (res.status === 200) Object.assign(form, res.data.data || {});
    })
    .catch(() => {});
}

function updateData() {
  isLoading.value = true;
  api
    .put(`/${routeName}/${rowSelected.value}`, form)
    .then(() => {
      Object.assign(form, formModel);
      Toast.fire({ icon: 'success', title: 'Actualizado correctamente' });
      modalHide('modal-teachers');
      getData();
      isLoading.value = false;
    })
    .catch((err) => {
      isLoading.value = false;
      errors.value = [];
      Object.getOwnPropertyNames(err.response?.data?.errors || {}).forEach((val) => {
        (err.response.data.errors[val] || []).forEach((el) => errors.value.push(el));
      });
      Toast.fire({ icon: 'error', title: 'Error' });
    });
}

function modalShow(modalName, data) {
  errors.value = [];
  if (!data) {
    modalType.value = 'add';
    Object.assign(form, formModel);
  } else {
    modalType.value = 'edit';
    rowSelected.value = data;
    getDataID();
  }
  const modal = new bootstrap.Modal(document.getElementById(modalName));
  modal.show();
}

function modalHide(modalName) {
  bootstrap.Modal.getOrCreateInstance(document.getElementById(modalName))?.hide();
}
</script>

<script>
import Swal from "sweetalert2";
</script>

<style lang="scss">
@import "vue-select/dist/vue-select.css";
</style>

<style scoped lang="scss">
.teacher-catalog-icon {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--bs-primary);
  color: white;
  border-radius: 10px;
}

.teacher-avatar {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 600;
  font-size: 0.9rem;
  flex-shrink: 0;
}

.teacher-catalog-table {
  --bs-table-hover-bg: rgba(var(--bs-primary-rgb), 0.04);
}

.bg-primary-subtle {
  background-color: rgba(var(--bs-primary-rgb), 0.15);
}

.bg-success-subtle {
  background-color: rgba(var(--bs-success-rgb), 0.15);
}

.bg-dark-subtle {
  background-color: rgba(var(--bs-dark-rgb), 0.1);
}
</style>
