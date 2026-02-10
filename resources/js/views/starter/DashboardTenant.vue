<script setup>
import { BarChart } from "vue-chart-3";
import { Chart, registerables } from "chart.js";
import ChartDataLabels from "chartjs-plugin-datalabels";
import _ from "underscore";
import { changeTimeZone } from "@/services/timezone";

onMounted(async () => {
  await getGrades();
  await getData();
  await getAssists();
});

const initialForm = {
  date_start: changeTimeZone("America/Mexico_City"),
  grade: null,
};
const form = reactive({ ...initialForm });
const records = ref([]);
const assists = ref([]);
const grades = ref([]);

const getGrades = async () => {
  const { data } = await api.get(`grades`);
  grades.value = data?.data ?? [];
  if (grades.value.length > 0) {
    form.grade = grades.value[0].description;
  }
};

const getData = async () => {
  const { data } = await api.get(`dashboard`);
  records.value = data;
};

const getAssists = async () => {
  const { data } = await api.get(`assists`, { params: form });
  assists.value = data.data;
};

const grouped = computed(() =>
  _.mapObject(_.groupBy(assists.value, "group"), (clist) =>
    clist.map((assist) => _.omit(assist, "student"))
  )
);
const labels = computed(() => _.keys(grouped.value));
const dataset = computed(() =>
  _.mapObject(grouped.value, function (val, key) {
    return val.length;
  })
);

Chart.register(...registerables, ChartDataLabels);

// Set Global Chart.js configuration
Chart.defaults.color = "#818d96";
Chart.defaults.scale.grid.lineWidth = 0;
Chart.defaults.scale.beginAtZero = true;
Chart.defaults.datasets.bar.maxBarThickness = 45;
Chart.defaults.elements.bar.borderRadius = 4;
Chart.defaults.elements.bar.borderSkipped = false;
Chart.defaults.elements.point.radius = 0;
Chart.defaults.elements.point.hoverRadius = 0;
Chart.defaults.plugins.tooltip.radius = 3;
Chart.defaults.plugins.legend.labels.boxWidth = 10;

// Chart Earnings data
const earningsData = reactive({
  labels: labels,
  datasets: [
    {
      label: "Asistencias",
      fill: true,
      data: dataset,
      backgroundColor: ["#77CEFF", "#0079AF", "#123E6B", "#97B0C4", "#A5C8ED"],
      borderColor: "transparent",
      pointBackgroundColor: "rgba(100, 116, 139, 1)",
      pointBorderColor: "#fff",
      pointHoverBackgroundColor: "#fff",
      pointHoverBorderColor: "rgba(100, 116, 139, 1)",
    },
  ],
});

// Chart Earnings options
const earningsOptions = reactive({
  scales: {
    x: {
      display: true,
      grid: {
        drawBorder: false,
      },
    },
    y: {
      display: true,
      grid: {
        drawBorder: false,
      },
    },
  },
  interaction: {
    intersect: false,
  },
  plugins: {
    legend: {
      labels: {
        boxHeight: 10,
        font: {
          size: 14,
        },
      },
    },
    tooltip: {
      callbacks: {
        label: function (context) {
          return context.dataset.label + ": " + context.parsed.y;
        },
      },
    },
  },
});

function printChart() {
  let labelString = labels.value.map((label) => `'${label}'`).join(",");
  let datasetString = Object.values(dataset.value).join(",");

  api({
    url: `/assists/export/pdf`,
    method: "POST",
    responseType: "blob",
    data: {
      labels: labels.value,
      labelString: labelString,
      dataset: dataset.value,
      datasetString: datasetString,
      date: form.date_start,
      grade: form.grade,
    },
  }).then((res) => {
    const url = window.URL.createObjectURL(new Blob([res.data]));
    const link = document.createElement("a");
    link.href = url;
    link.setAttribute(
      "download",
      `report-assist-${form.date_start}-${form.grade}.pdf`
    );
    document.body.appendChild(link);
    link.click();
  });
}
</script>

<template>
  <BasePageHeading title="Panel de Control" subtitle="Bienvenido Admin!" />

  <div class="content">
    <div class="row items-push">
      <div class="col-sm-6 col-xxl-3">
        <!-- Pending Orders -->
        <BaseBlock class="d-flex flex-column h-100 mb-0">
          <template #content>
            <div class="block-content block-content-full flex-grow-1 d-flex justify-content-between align-items-center">
              <dl class="mb-0">
                <dt class="fs-3 fw-bold">{{ records.students }}</dt>
                <dd class="fs-sm fw-medium fs-sm fw-medium text-muted mb-0">
                  Estudiantes
                </dd>
              </dl>
              <div class="item item-rounded-lg bg-body-light">
                <i class="fa-solid fa-children fs-3 text-primary"></i>
              </div>
            </div>
            <div class="bg-body-light rounded-bottom">
              <router-link :to="{ name: `students` }">
                <a
                  class="block-content block-content-full block-content-sm fs-sm fw-medium d-flex align-items-center justify-content-between">
                  <span>Ver todos estudiantes</span>
                  <i class="fa fa-arrow-alt-circle-right ms-1 opacity-25 fs-base"></i>
                </a>
              </router-link>
            </div>
          </template>
        </BaseBlock>
        <!-- END Pending Orders -->
      </div>
      <div class="col-sm-6 col-xxl-3">
        <!-- New Customers -->
        <BaseBlock class="d-flex flex-column h-100 mb-0">
          <template #content>
            <div class="block-content block-content-full flex-grow-1 d-flex justify-content-between align-items-center">
              <dl class="mb-0">
                <dt class="fs-3 fw-bold">{{ records.teachers }}</dt>
                <dd class="fs-sm fw-medium fs-sm fw-medium text-muted mb-0">
                  Profesores
                </dd>
              </dl>
              <div class="item item-rounded-lg bg-body-light">
                <i class="fa-solid fa-chalkboard-user fs-3 text-primary"></i>
              </div>
            </div>
            <div class="bg-body-light rounded-bottom">
              <router-link :to="{ name: `teachers` }">
                <a
                  class="block-content block-content-full block-content-sm fs-sm fw-medium d-flex align-items-center justify-content-between">
                  <span>Ver todos profesores</span>
                  <i class="fa fa-arrow-alt-circle-right ms-1 opacity-25 fs-base"></i>
                </a>
              </router-link>
            </div>
          </template>
        </BaseBlock>
        <!-- END New Customers -->
      </div>
      <div class="col-sm-6 col-xxl-3">
        <!-- Messages -->
        <BaseBlock class="d-flex flex-column h-100 mb-0">
          <template #content>
            <div class="block-content block-content-full flex-grow-1 d-flex justify-content-between align-items-center">
              <dl class="mb-0">
                <dt class="fs-3 fw-bold">{{ records.incidents }}</dt>
                <dd class="fs-sm fw-medium fs-sm fw-medium text-muted mb-0">
                  Incidencias
                </dd>
              </dl>
              <div class="item item-rounded-lg bg-body-light">
                <i class="far fa-paper-plane fs-3 text-primary"></i>
              </div>
            </div>
            <div class="bg-body-light rounded-bottom">
              <a class="block-content block-content-full block-content-sm fs-sm fw-medium d-flex align-items-center justify-content-between"
                href="javascript:void(0)">
                <span>Ver todas incidencias</span>
                <i class="fa fa-arrow-alt-circle-right ms-1 opacity-25 fs-base"></i>
              </a>
            </div>
          </template>
        </BaseBlock>
        <!-- END Messages -->
      </div>
      <div class="col-sm-6 col-xxl-3">
        <!-- Conversion Rate -->
        <BaseBlock class="d-flex flex-column h-100 mb-0">
          <template #content>
            <div class="block-content block-content-full flex-grow-1 d-flex justify-content-between align-items-center">
              <dl class="mb-0">
                <dt class="fs-3 fw-bold">4.5%</dt>
                <dd class="fs-sm fw-medium fs-sm fw-medium text-muted mb-0">
                  Conversion Rate
                </dd>
              </dl>
              <div class="item item-rounded-lg bg-body-light">
                <i class="fa fa-chart-bar fs-3 text-primary"></i>
              </div>
            </div>
            <div class="bg-body-light rounded-bottom">
              <a class="block-content block-content-full block-content-sm fs-sm fw-medium d-flex align-items-center justify-content-between"
                href="javascript:void(0)">
                <span>View statistics</span>
                <i class="fa fa-arrow-alt-circle-right ms-1 opacity-25 fs-base"></i>
              </a>
            </div>
          </template>
        </BaseBlock>
        <!-- END Conversion Rate-->
      </div>
    </div>

    <div class="row">
      <div class="col-xl-12 d-flex flex-column">
        <!-- Reporte Asistencias -->
        <BaseBlock title="Reporte Asistencias" class="flex-grow-1 d-flex flex-column">
          <template #options>
            <button type="button" class="btn-block-option" @click="printChart">
              <i class="fa-solid fa-print"></i>
            </button>
          </template>

          <template #content>
            <div class="container">
              <div class="row">
                <div class="col-md-6">
                  <label class="form-label">Fecha</label>
                  <input type="date" class="form-control" v-model="form.date_start" @change="getAssists" />
                </div>
                <div class="col-md-6">
                  <label class="form-label">Grado</label>
                  <select class="form-select" v-model="form.grade" @change="getAssists">
                    <option v-for="item in grades" :value="item.description">
                      {{ item.description }}
                    </option>
                  </select>
                </div>
              </div>
            </div>
            <div class="block-content block-content-full flex-grow-1 d-flex align-items-center">
              <BarChart :chart-data="earningsData" :options="earningsOptions" class="w-100" />
            </div>
          </template>
        </BaseBlock>
        <!-- END Reporte Asistencias -->
      </div>
    </div>

    <div class="row items-push">
      <div class="col-md-12">
        <BaseBlock title="Información del sistema" class="h-100 mb-0">
          <h6 class="text-dark">
            Sistema que permite a las escuelas llevar el control del alumno,
            como:
          </h6>

          <ul>
            <li>Expediente Digital</li>
            <li>Datos personales</li>
            <li>Historial clínico</li>
            <li>Historial de conducta escolar</li>
          </ul>
          <br />

          <h6 class="text-dark">
            El sistema también podrá registrar la asistencia de los alumnos, de
            forma semiautomática con su credencial escolar en la entrada de la
            escuela.
          </h6>

          <h6 class="text-dark">El Sistema puede imprimir:</h6>
          <ul>
            <li>Reportes de asistencia de los alumnos por Grado y Grupo</li>
            <li>Reportes de Incidencias de conducta Individual del Alumno</li>
            <li>Expediente digital del Alumno</li>
          </ul>
        </BaseBlock>
      </div>
    </div>
  </div>
</template>
