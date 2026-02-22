<template>
  <BasePageHeading :title="title">
    <template #extra>
      <router-link v-if="permissions.create" :to="{ name: `${routeName}.add` }">
        <button type="button" class="btn btn-alt-primary" v-click-ripple>
          <i class="fa fa-plus opacity-50 me-1"></i>
          Agregar
        </button>
      </router-link>
    </template>
  </BasePageHeading>

  <div class="content">
    <BaseBlock :title="titleBlock" content-full>
      <div class="text-center" v-if="isLoading">
        <div class="spinner-border text-dark" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
      <Dataset v-slot="{ ds }" :ds-data="dataFetched" :ds-sortby="sortBy" :ds-search-in="fieldsSearch" v-else>
        <div class="row" :data-page-count="ds.dsPagecount">
          <div id="datasetLength" class="col-md-8 py-2">
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
                    <th v-for="(th, index) in cols" :key="th.field" :class="['sort', th.sort]"
                      @click="onSort($event, index)">
                      {{ th.name }} <i class="gg-select float-end"></i>
                    </th>
                  </tr>
                </thead>
                <DatasetItem tag="tbody" class="fs-sm">
                  <template #default="{ row, rowIndex }">
                    <tr>
                      <th scope="row">{{ rowIndex + 1 }}</th>
                      <td v-for="field in fieldsSearch" :key="field">
                        <div v-if="row[`${field}`] === true">
                          <span class="badge bg-primary">Activo</span>
                        </div>
                        <div v-else-if="row[`${field}`] === false">
                          <span class="badge bg-danger">Inactivo</span>
                        </div>
                        <div v-else>
                          <span>{{ row[`${field}`] }}</span>
                        </div>
                      </td>
                      <td>
                        <div class="btn-group">
                          <router-link v-if="route.name === 'students'" :to="{
                            name: `students.detail`,
                            params: { id: row.id },
                          }">
                            <button type="button" class="btn btn-sm btn-alt-primary">
                              <i class="fa-solid fa-bars"></i>
                            </button>
                          </router-link>
                          <div v-if="route.name === 'roles'">
                            <router-link :to="{
                              name: `${routeName}.detail`,
                              params: { id: row.id },
                            }">
                              <button type="button" class="btn btn-sm btn-success">
                                <i class="fa-solid fa-list-check"></i>
                              </button>
                            </router-link>
                          </div>
                          <router-link
                            v-if="permissions.update && route.name != 'students' && route.name != 'reports.incidents'"
                            :to="{
                              name: `${routeName}.edit`,
                              params: { id: row.id },
                            }">
                            <button type="button" class="btn btn-sm btn-warning">
                              <i class="fa fa-fw fa-pencil-alt"></i>
                            </button>
                          </router-link>
                          <button v-if="permissions.delete && !(route.name === 'roles' && row.is_locked)" type="button" class="btn btn-sm btn-danger"
                            @click.prevent="destroy(row.id)">
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
        <div class="d-flex flex-md-row flex-column justify-content-between align-items-center">
          <DatasetInfo class="py-3 fs-sm" />
          <DatasetPager class="flex-wrap py-3 fs-sm" />
        </div>
      </Dataset>
    </BaseBlock>
  </div>
</template>

<script setup>
import { useDataTable } from "@/composables/datatable";
import Swal from "sweetalert2";
import {
  Dataset,
  DatasetItem,
  DatasetInfo,
  DatasetPager,
  DatasetSearch,
  DatasetShow,
} from "vue-dataset";
import moment from "moment/min/moment-with-locales";
import { useUserStore } from "@/stores/user";

moment.locale("es-mx");


const props = defineProps({
  title: String,
  titleBlock: String,
  fieldsSearch: Array,
  routeFetch: String,
  routeName: String,
  columns: Array,
  permissions: Object,
});
const { sortBy, onSort } = useDataTable();
const route = useRoute();
const dataFetched = ref([]);
const cols = reactive(props.columns);
const isLoading = ref(false)

onMounted(() => {
  console.log("Route Name: " + route.name);
  getData();
});

const userStore = useUserStore();

const getData = async () => {
  isLoading.value = true
  const { data } = await api.get(`/${props.routeFetch}`);
  if (
    route.name === "roles" ||
    route.name === "users" ||
    userStore.userCan("read all task") ||
    userStore.isSuperAdmin
  ) {
    dataFetched.value = data.data.reverse();
    isLoading.value = false
  } else {
    dataFetched.value = data.data.reverse().filter((el) => el.user_id === userStore.getUser.id);
    isLoading.value = false
  }
  isLoading.value = false
};

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

function modalShow(modalName, data) {
  var myModal = new bootstrap.Modal(document.getElementById(modalName));
  myModal.show();
}

function modalHide(modalName) {
  var myModal = bootstrap.Modal.getOrCreateInstance(
    document.getElementById(modalName)
  );
  myModal.hide();
}
</script>

<style lang="scss" scoped>
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
</style>
