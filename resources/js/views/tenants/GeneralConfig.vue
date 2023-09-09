<template>
  <BasePageHeading title="Configuración General" subtitle="Welcome Admin!" />

  <div class="content">
    <BaseBlock title="Configuración General" class="h-100">
      <form @submit.prevent="updateData">
        <div class="row push">
          <div class="col-lg-4">
            <p class="fs-sm text-center">
              <LoaderView v-if="isLoading" />
              <img v-else :src="`${form.logo}`" style="width: 50%" />
            </p>
          </div>

          <div class="col-lg-8 col-xl-5">
            <div class="mb-4">
              <label class="form-label">CCT</label>
              <input
                type="text"
                class="form-control"
                v-model="form.cct"
                disabled
              />
            </div>
            <div class="mb-4">
              <label class="form-label">Nombre Colegio</label>
              <input
                type="text"
                class="form-control"
                v-model="form.name"
                :disable="isLoading"
                v-uppercase
              />
            </div>
            <div class="mb-4">
              <label class="form-label">Modalidad</label>
              <VueSelect
                v-model="form.modality"
                :options="['MATUTINA', 'VESPERTINA', 'NOCTURNA']"
                :disable="isLoading"
                placeholder="Elige un valor..."
              />
            </div>
            <div class="mb-4">
              <label class="form-label">Dirección</label>
              <div class="form-check form-switch">
                <label class="form-check-label">Buscar</label>
                <input
                  v-model="searchAddress"
                  class="form-check-input"
                  type="checkbox"
                />
              </div>
              <input
                type="text"
                class="form-control"
                v-model="form.address"
                :disable="isLoading"
                v-if="!searchAddress"
              />
              <GMapAutocomplete
                v-else
                placeholder="Buscar dirección..."
                class="form-control"
                @place_changed="setPlace"
                :options="{
                  componentRestrictions: { country: 'mx' },
                  fields: ['geometry', 'formatted_address'],
                }"
              />
            </div>
            <div class="row mb-4">
              <label class="form-label">Ubicación</label>
              <GMapMap
                :center="center"
                :zoom="15"
                map-type-id="terrain"
                style="width: 100%; height: 20rem"
              >
                <GMapMarker
                  :key="index"
                  v-for="(m, index) in markers"
                  :position="m.position"
                  :clickable="true"
                  :draggable="true"
                  @dragend="updatePosition"
                />
              </GMapMap>
              <!-- <div class="col-6">
                <label class="form-label">{{ 'Latitud' }}</label>
                <input type="text" class="form-control" v-model="form.coordinates.lat" :disable="isLoading" />
              </div>
              <div class="col-6">
                <label class="form-label">{{ 'Longitud' }}</label>
                <input type="text" class="form-control" v-model="form.coordinates.lng" :disable="isLoading" />
              </div> -->
            </div>
            <div class="mb-4">
              <label class="form-label">Email</label>
              <input
                type="text"
                class="form-control"
                v-model="form.email"
                :disable="isLoading"
              />
            </div>
            <div class="mb-4">
              <label class="form-label">Teléfono</label>
              <input
                type="text"
                class="form-control"
                v-model="form.phone"
                :disable="isLoading"
              />
            </div>
            <div class="mb-4">
              <label class="form-label">Logo</label>
              <input
                class="form-control"
                type="file"
                accept="image/*"
                @change="onChangeFile"
              />
            </div>
            <div class="mb-4">
              <label class="form-label">Sitio Web</label>
              <input
                type="text"
                class="form-control"
                v-model="form.website"
                :disable="isLoading"
              />
            </div>
            <div class="mb-4">
              <label class="form-label">Último Número Matrícula</label>
              <input
                type="text"
                class="form-control"
                v-model="form.last_enrollment"
                :disable="isLoading"
              />
            </div>
            <div class="row mb-4">
              <div class="col-6">
                <label class="form-label">Plan Nombre</label>
                <select
                  class="form-select"
                  :disable="isLoading"
                  v-model="form.plan.name"
                >
                  <option value="Gratis">Gratis</option>
                  <option value="Full">Full</option>
                </select>
              </div>
              <div class="col-6">
                <label class="form-label">Plan Límite</label>
                <input
                  v-if="form.plan.name === 'Full'"
                  type="text"
                  class="form-control"
                  v-model="form.plan.limit"
                  disabled
                />
                <input
                  v-else
                  type="text"
                  class="form-control"
                  v-model="form.plan.limit"
                  :disable="isLoading"
                />
              </div>
            </div>
          </div>
        </div>
        <div class="row items-push">
          <div class="col-lg-4">
            <p class="fs-sm text-muted">Datos Fiscales</p>
          </div>
          <div class="col-lg-8">
            <div class="row mb-4">
              <div class="col-6">
                <label class="form-label">Nombre Facturación</label>
                <input
                  type="text"
                  class="form-control"
                  v-model="form.fiscal_data.billing_name"
                  :disable="isLoading"
                  v-uppercase
                />
              </div>
              <div class="col-6">
                <label class="form-label">RFC/RUC</label>
                <input
                  type="text"
                  class="form-control"
                  v-model="form.fiscal_data.rfc"
                  :disable="isLoading"
                  v-uppercase
                />
              </div>
            </div>
            <div class="row mb-4">
              <div class="col-6">
                <label class="form-label">Regimen Fiscal</label>
                <input
                  type="text"
                  class="form-control"
                  v-model="form.fiscal_data.tax_regime"
                  :disable="isLoading"
                  v-uppercase
                />
              </div>
              <div class="col-6">
                <label class="form-label">Código Postal</label>
                <input
                  type="text"
                  class="form-control"
                  v-model="form.fiscal_data.postal_code"
                  :disable="isLoading"
                  v-uppercase
                />
              </div>
            </div>
            <div class="row mb-4">
              <div class="col-12">
                <label class="form-label">Dirección Facturación</label>
                <input
                  type="text"
                  class="form-control"
                  v-model="form.fiscal_data.billing_address"
                  v-uppercase
                  :disable="isLoading"
                />
              </div>
            </div>
          </div>
        </div>
        <div class="row items-push">
          <div class="col-lg-4">
            <p class="fs-sm text-muted">Precios (MXN)</p>
          </div>
          <div class="col-lg-8">
            <div class="row mb-4">
              <div class="col-4">
                <label class="form-label">Credencial</label>
                <input
                  type="number"
                  class="form-control"
                  v-model="form.prices.credentials"
                  :disable="isLoading"
                />
              </div>
              <div class="col-4">
                <label class="form-label">Reingreso</label>
                <input
                  type="number"
                  class="form-control"
                  v-model="form.prices.reentry"
                  :disable="isLoading"
                />
              </div>
              <div class="col-4">
                <label class="form-label">Reposición</label>
                <input
                  type="number"
                  class="form-control"
                  v-model="form.prices.replacement"
                  :disable="isLoading"
                />
              </div>
            </div>
          </div>
        </div>
        <div class="row items-push">
          <div class="col-lg-4">
            <p class="fs-sm text-muted">Mensajes Personalizados</p>
          </div>
          <div class="col-lg-8">
            <div class="row mb-4">
              <div class="col-12">
                <label class="form-label">Incidencias WhatsApp</label>
                <textarea
                  class="form-control"
                  v-model="form.custom_messages.incidents"
                  rows="5"
                  placeholder="Textarea content.."
                  :disabled="isLoading"
                />
                <sub class="text-danger">
                  Puede reemplazar estas palabras comodin para personalizar el
                  mensaje:
                  <span @click="setWord('INCIDENCIA')">{INCIDENCIA}</span>,
                  <span @click="setWord('FECHA')">{FECHA}</span>,
                  <span @click="setWord('PROFESOR')">{PROFESOR}</span>,
                  <span @click="setWord('ESPECIALIDAD')">{ESPECIALIDAD}</span>,
                  <span @click="setWord('OBSERVACIONES')">{OBSERVACIONES}</span
                  >,
                  <span @click="setWord('ARCHIVO_URL')">{ARCHIVO_URL}</span>
                </sub>
              </div>
            </div>
          </div>
        </div>
        <div class="row push">
          <div class="col-lg-4"></div>

          <div class="col-lg-8 col-xl-5">
            <div class="mb-4">
              <button
                type="submit"
                class="btn btn-primary"
                :disabled="isLoading"
              >
                <i class="fa fa-cog fa-spin" v-if="isLoading"></i>
                <i class="fa-solid fa-floppy-disk" v-else></i>
                Actualizar
              </button>
            </div>
          </div>
        </div>
      </form>
    </BaseBlock>
  </div>
</template>

<script setup>
import VueSelect from "vue-select";
import api from "@/services/api";

onMounted(() => {
  getConfig();
  markers.value.push({
    position: center,
  });
});

function setWord(val) {
  form.value.custom_messages.incidents =
    form.value.custom_messages.incidents.concat(` {${val}}`);
}

function setPlace(location) {
  console.log(location);
  center.value = {
    lat: location.geometry.location.lat(),
    lng: location.geometry.location.lng(),
  };
  form.value.address = location.formatted_address;
  form.value.coordinates = center;
}

function updatePosition(location) {
  center.value = {
    lat: location.latLng.lat(),
    lng: location.latLng.lng(),
  };
  form.value.coordinates = center;
}

const center = ref({ lat: 19.425413016354454, lng: -99.12013237400363 });
const markers = ref([]);
const form = ref({
  name: "",
  cct: "",
  modality: "",
  address: "",
  coordinates: {
    lat: "",
    lng: "",
  },
  email: "",
  phone: "",
  website: "",
  fiscal_data: {},
  logo: "",
  last_enrollment: null,
  plan: {},
  prices: {},
  custom_messages: {},
});
const isLoading = ref(false);
const logo = ref();
const searchAddress = ref(false);

const getConfig = async () => {
  isLoading.value = true;
  try {
    const { data } = await api.get(`/general-configuration`);
    form.value = data;
  } catch (error) {
    console.error(error);
  }
  isLoading.value = false;
};

const updateData = async () => {
  api
    .post(`/general-configuration`, form.value)
    .then(() => {
      Toast.fire({
        icon: "success",
        title: "Configuración Actualizada",
      });
      getConfig();
    })
    .catch((err) => {
      Toast.fire({
        icon: "error",
        title: err.response.data.message,
      });
    });
};

function onChangeFile(e) {
  logo.value = e.target.files[0];
  uploadToCloudinary();
  isLoading.value = true;
}

function uploadToCloudinary() {
  const formData = new FormData();
  formData.append("file", logo.value);
  formData.append("upload_preset", "ceclxd0g");
  formData.append("api_key", "395568482732833");
  formData.append("timestamp", (Date.now() / 1000) | 0);

  return axios
    .post("https://api.cloudinary.com/v1_1/dofn0lfxx/upload", formData, {
      headers: { "X-Requested-With": "XMLHttpRequest" },
    })
    .then((response) => {
      const data = response.data;
      form.value.logo = data.secure_url;
      isLoading.value = false;
    });
}
</script>

<style lang="scss">
@import "vue-select/dist/vue-select.css";
@import "@/assets/scss/vendor/vue-select";
</style>
