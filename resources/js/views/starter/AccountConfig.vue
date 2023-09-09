<template>
  <BasePageHeading title="Configuración de Cuenta" subtitle="Welcome Admin!" />

  <div class="content">
    <BaseBlock title="Configuración de Cuenta" class="h-100">
      <form @submit.prevent="saveData">
        <div class="row">
          <div class="col-md-6">
            <label class="form-label"> País </label>
            <select class="form-select" v-model="form.country">
              <option v-for="(country, index) in countries" :key="index" :value="country.code">
                {{ country.name }}
              </option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label"> Zona Horaria </label>
            <select class="form-select" v-model="form.timezone">
              <option v-for="(timezone, index) in timeZones" :key="index" :value="timezone">
                {{ timezone }}
              </option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Ciudad</label>
            <input type="text" class="form-control" v-model="form.city" />
          </div>
          <div class="col-md-12 mt-3">
            <button type="submit" class="btn btn-primary">
              Guardar
            </button>
          </div>
        </div>
      </form>
    </BaseBlock>
  </div>
</template>

<script setup>
import { useUserStore } from "@/stores/user";

onMounted(() => {
  getUserConfig();
  getCountries();
  getTimeZones();
});

const form = ref({});
const timeZones = ref([]);
const countries = ref([]);
const userStore = useUserStore();

function saveData() {
  api
    .post(`/account-configuration/${userStore.getUser.id}`, form.value)
    .then(() => {
      Toast.fire({
        icon: "success",
        title: "Configuración Actualizada",
      });
      getUserConfig();
    })
    .catch((err) => {
      Toast.fire({
        icon: "error",
        title: err.response.data.message,
      });
    });
}

const getUserConfig = async () => {
  try {
    const { data } = await api.get(
      `/account-configuration/${userStore.getUser.id}`
    );
    form.value = data;
  } catch (error) {
    console.error(error);
  }
};

const getTimeZones = async () => {
  try {
    const { data } = await axios.get("http://worldtimeapi.org/api/timezone");
    timeZones.value = data;
  } catch (error) {
    console.error(error);
  }
};

const getCountries = async () => {
  try {
    const { data } = await axios.get("https://restcountries.com/v3.1/all");
    data.forEach((element) => {
      countries.value.push({
        name: element.name.common,
        code: element.cca2,
        timezones: element.timezones,
        flags: element.flags,
        capitalInfo: element.capitalInfo,
        currencies: element.currencies,
        capital: element.capital,
      });
    });
    countries.value.sort((a, b) =>
      a.name > b.name ? 1 : b.name > a.name ? -1 : 0
    );
  } catch (error) {
    console.error(error);
  }
};
</script>
