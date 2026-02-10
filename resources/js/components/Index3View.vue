<template>
  <BasePageHeading :title="title">
    <template #extra>
      <button
        v-if="permissions.create"
        type="button"
        class="btn btn-alt-primary"
        @click="modalShow(`modal-${routeName}`)"
      >
        <i class="fa fa-plus opacity-50 me-1"></i>
        Agregar
      </button>
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
                        <div
                          v-else-if="
                            moment(
                              row[`${field}`],
                              moment.ISO_8601,
                              true
                            ).isValid()
                          "
                        >
                          {{ formatDate(row[`${field}`]) }}
                        </div>
                        <div v-else>
                          <span v-if="field === 'id'">
                            <a
                              :href="`https://${
                                row[`${field}`]
                              }.${actualDomain}`"
                              target="_blank"
                            >
                              {{ `https://${row[`${field}`]}.${actualDomain}` }}
                            </a>
                          </span>
                          <span v-else-if="field === 'active'">
                            <span
                              :class="row[field] !== false ? 'badge bg-success' : 'badge bg-secondary'"
                            >
                              {{ row[field] !== false ? "Activo" : "Inactivo" }}
                            </span>
                          </span>
                          <span v-else>{{ row[`${field}`] }}</span>
                        </div>
                      </td>
                      <td v-if="columns.some((e) => e.field === 'action')">
                        <div class="btn-group">
                          <button
                            v-if="routeName === 'tenants'"
                            type="button"
                            :class="row.active !== false ? 'btn btn-sm btn-warning' : 'btn btn-sm btn-success'"
                            :title="row.active !== false ? 'Desactivar' : 'Activar'"
                            @click.prevent="toggleActive(row)"
                          >
                            <i :class="row.active !== false ? 'fa fa-fw fa-ban' : 'fa fa-fw fa-check'"></i>
                          </button>
                          <button
                            v-if="permissions.delete"
                            type="button"
                            class="btn btn-sm btn-danger"
                            title="Eliminar"
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
                  :disabled="isLoading"
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
</template>

<script setup>
import { useRoute } from "vue-router";
import { reactive, ref, onMounted } from "vue";
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
import moment from "moment/min/moment-with-locales";
import api from "@/services/api";
import map from "lodash/map";

moment.locale("es-mx");

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
});

const actualDomain = ref("");

const getData = async () => {
  const { data } = await api.get(`/${props.routeFetch}`);
  dataFetched.value = data;
};

function onSubmit() {
  if (modalType.value === "add") {
    saveData();
  } else {
    updateData();
  }
}

function saveData() {
  isLoading.value = true;
  api
    .post(`/${props.routeName}`, form)
    .then(() => {
      Object.assign(form, props.formModel);
      errors.value = [];
      Toast.fire({
        icon: "success",
        title: "Guardado Correctamente",
      });
      isLoading.value = false;
      getData();
      modalHide(`modal-${props.routeName}`);
    })
    .catch((err) => {
      console.log(err);
      errors.value = [];
      Object.getOwnPropertyNames(err.data.errors).forEach(function (val) {
        err.data.errors[val].forEach((element) => {
          errors.value.push(element);
        });
      });

      Toast.fire({
        icon: "error",
        title: "Error",
      });

      isLoading.value = false;
    });
}

function destroy(id) {
  Swal.fire({
    title: "¿Está seguro de eliminar?",
    showDenyButton: false,
    showCancelButton: true,
    confirmButtonText: "Sí",
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
          const msg = error.response?.data?.message || error.message;
          Toast.fire({
            icon: "error",
            title: msg,
          });
        });
    }
  });
}

function toggleActive(row) {
  const action = row.active !== false ? "desactivar" : "activar";
  Swal.fire({
    title: `¿${action.charAt(0).toUpperCase() + action.slice(1)} este cliente?`,
    showCancelButton: true,
    confirmButtonText: "Sí",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      api
        .patch(`/${props.routeFetch}/${row.id}/toggle-active`)
        .then((res) => {
          if (res.data?.success) {
            Toast.fire({
              icon: "success",
              title: res.data.message || "Actualizado",
            });
            getData();
          }
        })
        .catch((error) => {
          const msg = error.response?.data?.message || error.message;
          Toast.fire({
            icon: "error",
            title: msg,
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
        if (props.routeName === "equipments") {
          data.general != null ? data.general : (data.general = []);
          data.specifications != null
            ? data.specifications
            : (data.specifications = []);
        }
        Object.assign(form, data);
      }
    })
    .catch((err) => {
      console.log(err.response.data.errors);
    });
}

function updateData() {
  let updateForm = form;
  if (route.name === "equipments") {
    updateForm.general = JSON.stringify(updateForm.general);
    updateForm.specifications = JSON.stringify(updateForm.specifications);
  }

  api
    .put(`/${props.routeName}/${rowSelected.value}`, updateForm)
    .then(() => {
      Object.assign(form, props.formModel);
      Toast.fire({
        icon: "success",
        title: "Actualizado Correctamente",
      });
      modalHide(`modal-equipments-detail`);
      modalHide(`modal-${props.routeName}`);
      getData();
    })
    .catch((err) => {
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
