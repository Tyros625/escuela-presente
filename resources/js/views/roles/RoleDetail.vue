<template>
  <BasePageHeading title="Editar Permisos">
    <template #extra>
      <router-link :to="{ name: 'roles' }">
        <button type="button" class="btn btn-alt-primary" v-click-ripple>
          <i class="fa fa-arrow-left-long opacity-50 me-1"></i>
          Regresar
        </button>
      </router-link>
    </template>
  </BasePageHeading>

  <div class="content">
    <div class="row">
      <div class="col-md-3">
        <BaseBlock title="Detalles" ref="baseBlock">
          <ul class="nav nav-pills flex-column push">
            <li class="nav-item my-1">
              <a
                class="nav-link d-flex justify-content-between align-items-center"
                href="javascript:void(0)"
              >
                <span class="fs-sm"> Nombre </span>
                <span class="badge rounded-pill bg-black-50">
                  {{ restData.name }}
                </span>
              </a>
            </li>
            <li class="nav-item my-1">
              <a
                class="nav-link d-flex justify-content-between align-items-center"
                href="javascript:void(0)"
              >
                <span class="fs-sm"> Permisos Asignados </span>
                <span class="badge rounded-pill bg-black-50">
                  {{ assignedPermissions }}
                </span>
              </a>
            </li>
            <li class="nav-item my-1">
              <a
                class="nav-link d-flex justify-content-between align-items-center"
                href="javascript:void(0)"
              >
                <span class="fs-sm"> Total Usuarios </span>
                <span class="badge rounded-pill bg-black-50">
                  {{ restData.users_total }}
                </span>
              </a>
            </li>
          </ul>
          <div class="d-grid gap-2 mb-4">
            <button class="btn btn-primary" type="button" @click="saveChanges">
              <i class="fa-solid fa-floppy-disk"></i> Guardar
            </button>
          </div>
        </BaseBlock>
      </div>
      <div class="col-md-9">
        <div class="row">
          <div
            class="col-md-6"
            v-for="(module, key) in restData.system_modules"
            :key="key"
          >
            <BaseBlock
              :title="`${module.name} (${
                module.selected ? module.selected : 0
              })`"
              btn-option-content
            >
              <div
                class="form-check form-switch"
                v-for="(permission, idx) in module.permissions"
                :key="idx"
              >
                <input
                  class="form-check-input"
                  type="checkbox"
                  :checked="checkedIfExists(permission)"
                  @click="selectPermission(permission, key)"
                />
                <label class="form-check-label">
                  {{ permission }}
                </label>
              </div>
            </BaseBlock>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import BaseBlock from "@/components/BaseBlock.vue";

onMounted(() => {
  getRol();
});

const baseBlock = ref(null);
const route = useRoute();
const restData = reactive({});
const assignedPermissions = ref([]);
const permissionsSelected = ref([]);

const getRol = async () => {
  const { data } = await api.get(`/roles/${route.params.id}`);
  Object.assign(restData, data);
  assignedPermissions.value = restData.permissions.length;
  permissionsSelectedGenerate(restData.permissions);
  setPermissionsInit();
  localStorage.document = JSON.stringify(restData);
};

function permissionsSelectedGenerate(permissions) {
  permissionsSelected.value = permissions.map((item) => {
    return item.name;
  });
}

function setPermissionsInit() {
  for (const n in restData.system_modules) {
    restData.system_modules[n].selected = restData.system_modules[n].selected
      ? restData.system_modules[n].selected
      : 0;
    restData.system_modules[n].permissions.forEach((e1) =>
      permissionsSelected.value.forEach((e2) => {
        if (e1 === e2) {
          restData.system_modules[n].selected =
            restData.system_modules[n].selected + 1;
        }
      })
    );
  }
}

function checkedIfExists(permission) {
  if (permissionsSelected.value.indexOf(permission) !== -1) {
    return true;
  }
  return false;
}

function selectPermission(permission, idx) {
  restData.system_modules[idx].selected = restData.system_modules[idx].selected
    ? restData.system_modules[idx].selected
    : 0;

  let indexInSelected = permissionsSelected.value.indexOf(permission);
  let selectedThisPermission = indexInSelected !== -1;

  if (selectedThisPermission) {
    permissionsSelected.value.splice(indexInSelected, 1);
    restData.system_modules[idx].selected =
      restData.system_modules[idx].selected - 1;
    localStorage.document = JSON.stringify(restData);
    return true;
  }

  permissionsSelected.value.push(permission);
  restData.system_modules[idx].selected =
    restData.system_modules[idx].selected + 1;
  localStorage.document = JSON.stringify(restData);
  return true;
}

function saveChanges() {
  api
    .patch(`/roles/${route.params.id}/permissions`, {
      permissions: permissionsSelected.value,
    })
    .then(() => {
      localStorage.removeItem("permissions");
      localStorage.setItem(
        "permissions",
        JSON.stringify(permissionsSelected.value)
      );
      getRol();
      Toast.fire({
        icon: "success",
        title: "Permisos guardados correctamente",
      });
    });
}
</script>
