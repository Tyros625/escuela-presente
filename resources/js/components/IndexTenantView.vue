<template>
  <BasePageHeading :title="title">
    <template #extra>
      <div class="btn-group" role="group">
        <button
          v-if="import"
          type="button"
          class="btn btn-success"
          @click="modalShow(`modal-import`)"
        >
          <i class="fa-solid fa-file-excel"></i> Importar
        </button>
        <button
          v-if="permissions.create"
          type="button"
          class="btn btn-alt-primary"
          @click="modalShow(`modal-${routeName}`)"
        >
          <i class="fa fa-plus opacity-50 me-1"></i>
          Agregar
        </button>
        <button
          v-if="export"
          type="button"
          class="btn btn-alt-success"
          @click="exportFile"
        >
          <i class="fa-solid fa-cloud-arrow-down"></i>
          Exportar
        </button>
      </div>
    </template>
  </BasePageHeading>

  <div class="content">
    <BaseBlock :title="titleBlock" content-full>
      <Dataset
        v-slot="{ ds }"
        :ds-data="dataFetched"
        :ds-sortby="sortBy"
        :ds-search-in="fieldsSearch"
      >
        <div class="row" :data-page-count="ds.dsPagecount">
          <div id="datasetLength" class="col-md-3 py-2">
            <DatasetShow />
          </div>
          <div class="col-md-4 py-2">
            <DatasetSearch ds-search-placeholder="Buscar..." />
          </div>
        </div>
        <hr />
        <div class="row">
          <div class="col-md-12">
            <div class="table-responsive">
              <table class="table table-striped mb-0">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th
                      v-for="(th, index) in cols"
                      :key="th.field"
                      :class="['sort', th.sort]"
                      @click="onSort($event, index)"
                    >
                      {{ th.name }} <i class="gg-select float-end"></i>
                    </th>
                  </tr>
                </thead>
                <DatasetItem tag="tbody" class="fs-sm">
                  <template #default="{ row, rowIndex }">
                    <tr>
                      <th scope="row">{{ rowIndex + 1 }}</th>
                      <td v-for="field in fieldsSearch" :key="field">
                        <div v-if="typeof row[`${field}`] === 'object'">
                          <span>
                            {{ map(row[`${field}`], "name").join(", ") }}
                          </span>
                        </div>
                        <div v-else>
                          <span v-if="field === 'id'">
                            <a
                              :href="`http://${
                                row[`${field}`]
                              }.${actualDomain}`"
                              target="_blank"
                            >
                              {{ `http://${row[`${field}`]}.${actualDomain}` }}
                            </a>
                          </span>
                          <span v-else-if="field === 'student_photo'">
                            <a :href="row[`${field}`]" target="_blank">
                              Ver Fotografía
                            </a>
                          </span>
                          <span v-else>{{ row[`${field}`] }}</span>
                        </div>
                      </td>
                      <td v-if="columns.some((e) => e.field === 'action')">
                        <div class="btn-group">
                          <button
                            v-if="permissions.update"
                            type="button"
                            class="btn btn-sm btn-warning"
                            @click.prevent="
                              modalShow(`modal-${routeName}`, row.id)
                            "
                          >
                            <i class="fa-solid fa-pen"></i>
                          </button>
                          <button
                            v-if="permissions.delete"
                            type="button"
                            class="btn btn-sm btn-danger"
                            @click.prevent="destroy(row.id)"
                          >
                            <i class="fa fa-fw fa-times"></i>
                          </button>
                        </div>
                      </td>
                    </tr>
                  </template>
                </DatasetItem>
              </table>
            </div>
          </div>
        </div>
        <div
          class="d-flex flex-md-row flex-column justify-content-between align-items-center"
        >
          <DatasetInfo class="py-3 fs-sm" />
          <DatasetPager class="flex-wrap py-3 fs-sm" />
        </div>
      </Dataset>
    </BaseBlock>
  </div>

  <!-- Modal -->
  <div
    class="modal fade"
    :id="`modal-${routeName}`"
    tabindex="-1"
    :aria-labelledby="`modal-${routeName}-label`"
    aria-hidden="true"
    data-bs-backdrop="static"
  >
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" :id="`modal-${routeName}-label`">
            {{ modalType === "add" ? "Agregar" : "Editar" }}
          </h5>
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"
          ></button>
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
                  :placeholder="el.placeholder"
                  :min="el.min"
                  :max="el.max"
                  :step="el.step"
                  :disabled="isLoading"
                  v-uppercase
                />
                <VueSelect
                  v-if="el.type === 'select'"
                  v-model="form[el.model]"
                  :options="el.values"
                  :label="el.labelApi"
                  :reduce="(option) => option.id"
                  placeholder="Elige un valor..."
                  :disabled="isLoading"
                />
                <VueSelect
                  v-if="el.type === 'select-multiple'"
                  v-model="form[el.model]"
                  :options="el.values"
                  :label="el.labelApi"
                  :reduce="(option) => option.id"
                  placeholder="Elige un valor..."
                  multiple
                  :disabled="isLoading"
                />
                <div v-if="el.type === 'subjects'">
                  <div
                    v-for="i in Math.max(0, Number(form[el.countModel] || 0))"
                    :key="i"
                    class="mb-2"
                  >
                    <input
                      type="text"
                      v-model="form[el.model][i - 1]"
                      class="form-control"
                      :placeholder="`${el.itemPlaceholder || 'Materia'} ${i}`"
                      :disabled="isLoading"
                      v-uppercase
                    />
                  </div>
                </div>
              </div>
              <div class="col-md-12 mt-3 text-end">
                <button
                  type="submit"
                  class="btn btn-primary"
                  :disabled="isLoading"
                >
                  <i class="fa fa-cog fa-spin" v-if="isLoading"></i>
                  <i class="fa-solid fa-floppy-disk" v-else></i>
                  {{ modalType === "add" ? "Guardar" : "Actualizar" }}
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal -->
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
          <h1 class="modal-title fs-5" id="modal-import-label">
            Importar {{ title }}
          </h1>
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"
          ></button>
        </div>
        <div class="modal-body">
          <ErrorsView v-if="errors.length" :errors="errors" />
          <form @submit.prevent="importXLS">
            <div class="mb-3">
              <label for="formFile" class="form-label"
                >Seleccione archivo</label
              >
              <input
                class="form-control"
                type="file"
                id="formFile"
                @change="onChangeFile"
              />
            </div>
            <div class="mb-3 text-center">
              <a href="/imports/profesores.xlsx">Descargar Ejemplo</a>
            </div>
            <div class="col-md-12 mt-3 text-end">
              <button
                type="submit"
                class="btn btn-primary"
                :disabled="isLoading"
              >
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
import { useDataTable } from "@/composables/datatable";
import {
  Dataset,
  DatasetItem,
  DatasetInfo,
  DatasetPager,
  DatasetSearch,
  DatasetShow,
} from "vue-dataset";
import Swal from "sweetalert2";
import VueSelect from "vue-select";
import api from "@/services/api";
import map from "lodash/map";
import moment from "moment";

const props = defineProps({
  title: String,
  titleBlock: String,
  fieldsSearch: Array,
  routeFetch: String,
  routeName: String,
  columns: Array,
  formSchema: Array,
  formModel: Object,
  permissions: Object,
  import: {
    type: Boolean,
    default: false,
  },
  export: {
    type: Boolean,
    default: false,
  },
});
const form = reactive({});
const route = useRoute();
const dataFetched = ref([]);
const errors = ref([]);
const { sortBy, onSort } = useDataTable();
const cols = reactive(props.columns);
const modalType = ref();
const rowSelected = ref(null);
const isLoading = ref(false);

function formatDate(date) {
  return moment(date).format("DD/MM/YY, h:mm a");
}

onMounted(async () => {
  console.log("Route Name: " + route.name);
  actualDomain.value = window.location.host;
  getData();
  setupSubjectsWatchers();
});

const actualDomain = ref("");

function normalizeSubjectsField(el) {
  const count = Math.max(0, Number(form[el.countModel] || 0));
  if (!Array.isArray(form[el.model])) form[el.model] = [];
  if (form[el.model].length > count) {
    form[el.model] = form[el.model].slice(0, count);
  } else {
    while (form[el.model].length < count) form[el.model].push("");
  }
}

function setupSubjectsWatchers() {
  props.formSchema
    .filter((el) => el.type === "subjects")
    .forEach((el) => {
      watch(
        () => form[el.countModel],
        () => normalizeSubjectsField(el),
        { immediate: true }
      );
    });
}

const getData = async () => {
  const { data } = await api.get(`/${props.routeFetch}`);
  dataFetched.value = data.data;
};

function onSubmit() {
  if (modalType.value === "add") {
    saveData();
  } else {
    updateData();
  }
}

const file = ref();

function onChangeFile(e) {
  console.log(e);
  file.value = e.target.files[0];
}

function importXLS() {
  const formData = new FormData();
  formData.append("file", file.value);

  api
    .post(`/${props.routeName}/import`, formData)
    .then(async function (res) {
      Toast.fire({
        icon: "success",
        title: res.data.message,
      });
      modalHide(`modal-import`);
      await getData();
    })
    .catch(function (err) {
      errors.value = [];
      isLoading.value = false;
      Object.getOwnPropertyNames(err.data.errors).forEach(function (val) {
        err.data.errors[val].forEach((element) => {
          errors.value.push(element);
        });
      });

      Toast.fire({
        icon: "error",
        title: "Error",
      });
    });
}

function saveData() {
  isLoading.value = true;
  api
    .post(`/${props.routeName}`, form)
    .then(() => {
      isLoading.value = false;
      Object.assign(form, props.formModel);
      errors.value = [];
      Toast.fire({
        icon: "success",
        title: "Guardado Correctamente",
      });
      getData();
      modalHide(`modal-${props.routeName}`);
    })
    .catch((err) => {
      errors.value = [];
      isLoading.value = false;
      Object.getOwnPropertyNames(err.data.errors).forEach(function (val) {
        err.data.errors[val].forEach((element) => {
          errors.value.push(element);
        });
      });

      Toast.fire({
        icon: "error",
        title: "Error",
      });
    });
}

function destroy(id) {
  Swal.fire({
    title: "¿Estás segurx de eliminar?",
    showDenyButton: false,
    showCancelButton: true,
    confirmButtonText: "Si",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      api
        .delete(`/${props.routeFetch}/${id}`)
        .then((res) => {
          if (res.status === 200) {
            Toast.fire({
              icon: "success",
              title: "Eliminado correctamente",
            });
            getData();
          }
        })
        .catch((error) => {
          Toast.fire({
            icon: "error",
            title: error.message,
          });
        });
    }
  });
}

function getDataID() {
  api
    .get(`/${props.routeFetch}/${rowSelected.value}`)
    .then((res) => {
      if (res.status === 200) {
        let data = res.data.data;
        Object.assign(form, data);
        props.formSchema
          .filter((el) => el.type === "subjects")
          .forEach((el) => {
            if (!form[el.countModel]) {
              form[el.countModel] = Array.isArray(form[el.model])
                ? form[el.model].length
                : 0;
            }
            normalizeSubjectsField(el);
          });
      }
    })
    .catch((err) => {
      console.log(err.response.data.errors);
    });
}

function exportFile() {
  let date = moment().format("DD/MM/YY h:mm a");

  api({
    url: `/${props.routeFetch}/export`,
    method: "GET",
    responseType: "blob",
  }).then((res) => {
    const url = window.URL.createObjectURL(new Blob([res.data]));
    const link = document.createElement("a");
    link.href = url;
    link.setAttribute("download", `${props.routeFetch}-${date}.xlsx`);
    document.body.appendChild(link);
    link.click();
  });
}

function updateData() {
  isLoading.value = true;
  let updateForm = form;

  api
    .put(`/${props.routeName}/${rowSelected.value}`, updateForm)
    .then(() => {
      Object.assign(form, props.formModel);
      Toast.fire({
        icon: "success",
        title: "Actualizado Correctamente",
      });
      modalHide(`modal-${props.routeName}`);
      getData();
      isLoading.value = false;
    })
    .catch((err) => {
      isLoading.value = false;
      errors.value = [];
      Object.getOwnPropertyNames(err.response.data.errors).forEach(function (
        val
      ) {
        err.response.data.errors[val].forEach((element) => {
          errors.value.push(element);
        });
      });

      Toast.fire({
        icon: "error",
        title: "Error",
      });
    });
}

function modalShow(modalName, data) {
  errors.value = [];
  if (!data) {
    modalType.value = "add";
    Object.assign(form, props.formModel);
  } else {
    modalType.value = "edit";
    rowSelected.value = data;
    getDataID();
  }

  var myModal = new bootstrap.Modal(document.getElementById(modalName));
  myModal.show();
}

function modalHide(modalName) {
  console.log(modalName);
  var myModal = bootstrap.Modal.getOrCreateInstance(
    document.getElementById(modalName)
  );
  myModal.hide();
}
</script>

<style lang="scss">
// Vue Select + Custom overrides
@import "vue-select/dist/vue-select.css";
@import "@/assets/scss/vendor/vue-select";

.gg-select {
  box-sizing: border-box;
  position: relative;
  display: block;
  transform: scale(1);
  width: 22px;
  height: 22px;
}

.gg-select::after,
.gg-select::before {
  content: "";
  display: block;
  box-sizing: border-box;
  position: absolute;
  width: 8px;
  height: 8px;
  left: 7px;
  transform: rotate(-45deg);
}

.gg-select::before {
  border-left: 2px solid;
  border-bottom: 2px solid;
  bottom: 4px;
  opacity: 0.3;
}

.gg-select::after {
  border-right: 2px solid;
  border-top: 2px solid;
  top: 4px;
  opacity: 0.3;
}

th.sort {
  cursor: pointer;
  user-select: none;

  &.asc {
    .gg-select::after {
      opacity: 1;
    }
  }

  &.desc {
    .gg-select::before {
      opacity: 1;
    }
  }
}

/* .pac-container {
  background-color: #fff;
  z-index: 20;
  position: fixed;
  display: inline-block;
  float: left;
}

.modal {
  z-index: 20;
}

.modal-backdrop {
  z-index: 10;
} */
</style>
