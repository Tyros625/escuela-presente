<script setup>
const groups = ref([]);
const selectedGroupId = ref(null);
const isLoadingGroups = ref(false);

const activeTab = ref("capture");

const captureRows = ref([]);
const isLoadingCapture = ref(false);

const searchLastName = ref("");
const receiptStudentOptions = computed(() =>
  captureRows.value.map((row) => ({
    label: row.name,
    value: row.grade_id,
  }))
);
const selectedReceiptGradeId = ref(null);
const receiptData = ref(null);
const isLoadingReceipt = ref(false);

const historyRows = ref([]);
const isLoadingHistory = ref(false);

onMounted(async () => {
  await fetchGroups();
});

async function fetchGroups() {
  isLoadingGroups.value = true;
  try {
    const { data } = await api.get("qualification/groups");
    groups.value = data.data;
    if (groups.value.length > 0) {
      selectedGroupId.value = groups.value[0].id;
      await fetchCapture();
      await fetchHistory();
    }
  } catch (error) {
    console.error(error);
    Toast.fire({
      icon: "error",
      title: "No se pudieron cargar los grupos",
    });
  } finally {
    isLoadingGroups.value = false;
  }
}

watch(selectedGroupId, async (newVal, oldVal) => {
  if (newVal && newVal !== oldVal) {
    await fetchCapture();
    await fetchHistory();
    receiptData.value = null;
    selectedReceiptGradeId.value = null;
  }
});

async function fetchCapture() {
  if (!selectedGroupId.value) return;
  isLoadingCapture.value = true;
  try {
    const { data } = await api.get(
      `qualification/groups/${selectedGroupId.value}/grades`
    );
    captureRows.value = data.data.students || [];
  } catch (error) {
    console.error(error);
    Toast.fire({
      icon: "error",
      title: "No se pudieron cargar las calificaciones",
    });
  } finally {
    isLoadingCapture.value = false;
  }
}

async function updatePartial(row, field, value) {
  if (!row.grade_id) {
    Toast.fire({
      icon: "error",
      title: "Primero debe existir un registro de calificación",
    });
    return;
  }

  const numericValue = parseFloat(value);
  if (Number.isNaN(numericValue) || numericValue < 0 || numericValue > 10) {
    Toast.fire({
      icon: "error",
      title: "El valor debe ser un número entre 0 y 10",
    });
    return;
  }

  const reason = window.prompt("Motivo del cambio de calificación:");
  if (reason === null || String(reason).trim() === "") {
    return;
  }

  try {
    const { data } = await api.put(`qualification/grades/${row.grade_id}`, {
      field,
      value: numericValue,
      reason,
    });
    const updated = data.data;
    row.partial_1 = updated.partial_1;
    row.partial_2 = updated.partial_2;
    row.partial_3 = updated.partial_3;
    row.average = updated.average;
    row.status = updated.status;
    await fetchHistory();
  } catch (error) {
    console.error(error);
    Toast.fire({
      icon: "error",
      title: "No se pudo actualizar la calificación",
    });
  }
}

const filteredCaptureRows = computed(() => {
  if (!searchLastName.value) {
    return captureRows.value;
  }
  const term = searchLastName.value.toLowerCase();
  return captureRows.value.filter((row) =>
    row.name.toLowerCase().startsWith(term)
  );
});

watch(searchLastName, () => {
  // keep computed list in sync; no-op body
});

async function fetchReceipt() {
  if (!selectedReceiptGradeId.value) return;
  isLoadingReceipt.value = true;
  try {
    const { data } = await api.get(
      `qualification/grades/${selectedReceiptGradeId.value}/receipt`
    );
    receiptData.value = data.data;
  } catch (error) {
    console.error(error);
    Toast.fire({
      icon: "error",
      title: "No se pudo cargar la boleta",
    });
  } finally {
    isLoadingReceipt.value = false;
  }
}

async function exportReceiptPdf() {
  if (!selectedReceiptGradeId.value) return;
  const url = `${window.location.origin}/api/qualification/grades/${selectedReceiptGradeId.value}/receipt?format=pdf`;
  window.open(url, "_blank");
}

async function fetchHistory() {
  if (!selectedGroupId.value) return;
  isLoadingHistory.value = true;
  try {
    const { data } = await api.get("qualification/grades/history", {
      params: { group_id: selectedGroupId.value },
    });
    historyRows.value = data.data || [];
  } catch (error) {
    console.error(error);
    Toast.fire({
      icon: "error",
      title: "No se pudo cargar el historial",
    });
  } finally {
    isLoadingHistory.value = false;
  }
}
</script>

<template>
  <BasePageHeading title="Calificaciones · Qualification Record" />

  <div class="content">
    <BaseBlock title="Grupo y materia" content-full>
      <div class="row mb-3">
        <div class="col-md-6">
          <label class="form-label">Grupo</label>
          <select
            class="form-select"
            v-model="selectedGroupId"
            :disabled="isLoadingGroups"
          >
            <option :value="null">Seleccione un grupo</option>
            <option
              v-for="group in groups"
              :key="group.id"
              :value="group.id"
            >
              {{ group.name }} -
              {{ group.grade?.description }} {{ group.section?.description }}
              ({{ group.school_cycle?.description }})
            </option>
          </select>
        </div>
      </div>
    </BaseBlock>

    <BaseBlock class="mt-3" content-full>
      <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
          <button
            class="nav-link"
            :class="{ active: activeTab === 'capture' }"
            type="button"
            @click="activeTab = 'capture'"
          >
            Registrar evaluaciones
          </button>
        </li>
        <li class="nav-item">
          <button
            class="nav-link"
            :class="{ active: activeTab === 'receipt' }"
            type="button"
            @click="activeTab = 'receipt'"
          >
            Consultar boleta
          </button>
        </li>
        <li class="nav-item">
          <button
            class="nav-link"
            :class="{ active: activeTab === 'history' }"
            type="button"
            @click="activeTab = 'history'"
          >
            Historial de cambios
          </button>
        </li>
      </ul>

      <div v-if="activeTab === 'capture'">
        <div class="row mb-3">
          <div class="col-md-4">
            <label class="form-label">Buscar por apellido</label>
            <input
              type="text"
              v-model="searchLastName"
              class="form-control"
              placeholder="Apellido"
            />
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-sm table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>Matrícula</th>
                <th>Nombre</th>
                <th>Parcial 1</th>
                <th>Parcial 2</th>
                <th>Parcial 3</th>
                <th>Promedio</th>
                <th>Estado</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="row in filteredCaptureRows"
                :key="`${row.student_id}-${row.grade_id || 'new'}`"
              >
                <td>{{ row.list_number }}</td>
                <td>{{ row.enrollment }}</td>
                <td>{{ row.name }}</td>
                <td>
                  <input
                    type="number"
                    step="0.1"
                    min="0"
                    max="10"
                    class="form-control form-control-sm"
                    :value="row.partial_1"
                    @change="(e) => updatePartial(row, 'partial_1', e.target.value)"
                  />
                </td>
                <td>
                  <input
                    type="number"
                    step="0.1"
                    min="0"
                    max="10"
                    class="form-control form-control-sm"
                    :value="row.partial_2"
                    @change="(e) => updatePartial(row, 'partial_2', e.target.value)"
                  />
                </td>
                <td>
                  <input
                    type="number"
                    step="0.1"
                    min="0"
                    max="10"
                    class="form-control form-control-sm"
                    :value="row.partial_3"
                    @change="(e) => updatePartial(row, 'partial_3', e.target.value)"
                  />
                </td>
                <td>{{ row.average }}</td>
                <td>
                  <span
                    v-if="row.status"
                    :class="[
                      'fw-semibold',
                      row.status === 'approved' ? 'text-success' : 'text-danger',
                    ]"
                  >
                    {{ row.status === "approved" ? "Aprobado" : "Reprobado" }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div v-else-if="activeTab === 'receipt'">
        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Alumno</label>
            <select
              class="form-select"
              v-model="selectedReceiptGradeId"
              :disabled="!receiptStudentOptions.length"
            >
              <option :value="null">Seleccione un alumno</option>
              <option
                v-for="option in receiptStudentOptions"
                :key="option.value"
                :value="option.value"
              >
                {{ option.label }}
              </option>
            </select>
          </div>
          <div class="col-md-3 d-flex align-items-end">
            <button
              type="button"
              class="btn btn-primary me-2"
              :disabled="!selectedReceiptGradeId || isLoadingReceipt"
              @click="fetchReceipt"
            >
              <i class="fa fa-cog fa-spin" v-if="isLoadingReceipt"></i>
              <i class="fa-solid fa-magnifying-glass" v-else></i>
              Ver boleta
            </button>
            <button
              type="button"
              class="btn btn-secondary"
              :disabled="!selectedReceiptGradeId"
              @click="exportReceiptPdf"
            >
              <i class="fa-solid fa-print"></i>
              Imprimir PDF
            </button>
          </div>
        </div>

        <div v-if="receiptData" class="border rounded p-3">
          <h4 class="mb-3">
            {{
              `${receiptData.student.last_name_father} ${receiptData.student.last_name_mother}, ${receiptData.student.name}`
            }}
          </h4>
          <p class="mb-1">
            <strong>Matrícula:</strong> {{ receiptData.student.enrollment }}
          </p>
          <p class="mb-1">
            <strong>Grupo:</strong>
            {{ receiptData.group.grade }} {{ receiptData.group.section }}
            ({{ receiptData.group.school_cycle }})
          </p>

          <hr />

          <p class="mb-1">
            <strong>Parcial 1:</strong>
            {{ receiptData.grades.partial_1 }}
          </p>
          <p class="mb-1">
            <strong>Parcial 2:</strong>
            {{ receiptData.grades.partial_2 }}
          </p>
          <p class="mb-1">
            <strong>Parcial 3:</strong>
            {{ receiptData.grades.partial_3 }}
          </p>
          <p class="mb-1">
            <strong>Promedio:</strong>
            {{ receiptData.grades.average }}
          </p>
          <p class="mb-1">
            <strong>Estado:</strong>
            <span
              :class="[
                'fw-semibold',
                receiptData.grades.status === 'approved'
                  ? 'text-success'
                  : 'text-danger',
              ]"
            >
              {{
                receiptData.grades.status === "approved"
                  ? "Aprobado"
                  : "Reprobado"
              }}
            </span>
          </p>
        </div>
      </div>

      <div v-else-if="activeTab === 'history'">
        <div class="table-responsive">
          <table class="table table-sm table-striped">
            <thead>
              <tr>
                <th>Fecha</th>
                <th>Alumno</th>
                <th>Materia</th>
                <th>Campo</th>
                <th>Antes</th>
                <th>Después</th>
                <th>Motivo</th>
                <th>Modificado por</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(row, index) in historyRows" :key="index">
                <td>{{ row.changed_at }}</td>
                <td>{{ row.student_name }}</td>
                <td>{{ row.subject_name }}</td>
                <td>{{ row.field_changed }}</td>
                <td>{{ row.old_value }}</td>
                <td>{{ row.new_value }}</td>
                <td>{{ row.reason }}</td>
                <td>{{ row.changed_by }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </BaseBlock>
  </div>
</template>

