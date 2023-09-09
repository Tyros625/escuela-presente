<template>
  <BasePageHeading title="Asistencias" />

  <div class="content">
    <BaseBlock title="Filtros de Búsqueda">
      <form @submit.prevent="submit" class="mb-4">
        <div class="row">
          <div class="col-md-3">
            <label class="form-label">Tipo</label>
            <select class="form-select" v-model="form.type">
              <option value="lessons">Clases</option>
              <option value="dinning">Comedor</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Fecha</label>
            <input type="date" class="form-control" v-model="form.date_start" />
          </div>
          <div class="col-md-3">
            <label class="form-label">Grado</label>
            <select class="form-select" v-model="form.grade">
              <option :value="null">- Ninguno -</option>
              <option v-for="item in grades" :value="item.description">
                {{ item.description }}
              </option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Grupo</label>
            <select class="form-select" v-model="form.group">
              <option :value="null">- Ninguno -</option>
              <option v-for="item in sections" :value="item.description">
                {{ item.description }}
              </option>
            </select>
          </div>

          <div class="col-md-12 mt-3">
            <button type="sucess" class="btn btn-primary" :disabled="isLoading">
              <i class="fa fa-cog fa-spin" v-if="isLoading"></i>
              <i class="fa-solid fa-magnifying-glass" v-else></i>
              Consultar
            </button>
            <button
              type="button"
              class="btn btn-secondary"
              style="margin-left: 10px"
              :disabled="isLoading"
              @click="printData"
            >
              <i class="fa fa-cog fa-spin" v-if="isLoading"></i>
              <i class="fa-solid fa-print" v-else></i>
              Imprimir
            </button>
          </div>

          <div class="col-md-12 mt-3" id="printTable">
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Estudiante</th>
                    <th scope="col">Fecha/Hora</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in records" :key="index">
                    <th scope="row">{{ ++index }}</th>
                    <td>
                      {{
                        `${item.student.last_name_father} ${item.student.last_name_mother}, ${item.student.name}`
                      }}
                    </td>
                    <td>{{ `${item.created_at}` }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </form>
    </BaseBlock>
  </div>
</template>

<script setup>
import { useStudentStore } from "@/stores/student";
import { changeTimeZone } from "@/services/timezone";

onMounted(async () => {
  await getGrades();
  await getSections();
});

const initialForm = {
  type: "lessons",
  date_start: changeTimeZone("America/Mexico_City"),
  grade: null,
  group: null,
  type_balance: "expense",
};
const form = reactive({ ...initialForm });
const studentStore = useStudentStore();
const records = ref([]);
const isLoading = ref(false);
const router = useRouter();
const grades = ref([]);
const sections = ref([]);

const getGrades = async () => {
  const { data } = await api.get(`grades`);
  grades.value = data.data;
};

const getSections = async () => {
  const { data } = await api.get(`sections`);
  sections.value = data.data;
};

function submit() {
  isLoading.value = true;

  if (form.type === "lessons") {
    api
      .get(`assists`, { params: form })
      .then((res) => {
        records.value = res.data.data;
        studentStore.data_report = res.data.data;
        studentStore.form_print = form;
        isLoading.value = false;
      })
      .catch((err) => {
        console.log(err.response);
        isLoading.value = false;
      });
  } else {
    api
      .get(`balances`, { params: form })
      .then((res) => {
        records.value = res.data.data;
        isLoading.value = false;
      })
      .catch((err) => {
        console.log(err.response);
        isLoading.value = false;
      });
  }
}

function printData() {
  router.push({
    name: "print",
  });
}
</script>
