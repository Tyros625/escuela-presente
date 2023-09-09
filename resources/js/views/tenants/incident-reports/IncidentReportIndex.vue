<template>
  <!-- <BasePageHeading title="Estudiantes" /> -->
  <BasePageHeading title="Incidencias">
    <template #extra>
      <router-link :to="{ name: `incident-reports.add` }">
        <button type="button" class="btn btn-alt-primary" v-click-ripple>
          <i class="fa fa-plus opacity-50 me-1"></i>
          Agregar
        </button>
      </router-link>
    </template>
  </BasePageHeading>

  <div class="content">
    <BaseBlock title="Filtros de Búsqueda">
      <form @submit.prevent="submit" class="mb-4">
        <div class="row">
          <div class="col-md-6">
            <label class="form-label">Estudiante</label>
            <input
              class="form-control"
              type="text"
              v-model="form.student"
              v-uppercase
            />
          </div>

          <div class="col-md-12 mt-3">
            <button type="sucess" class="btn btn-primary" :disabled="isLoading">
              <i class="fa fa-cog fa-spin" v-if="isLoading"></i>
              <i class="fa-solid fa-magnifying-glass" v-else></i>
              Consultar
            </button>
          </div>

          <div class="col-md-12 mt-3">
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Estudiante</th>
                    <th scope="col">Incidente</th>
                    <th scope="col">Profesor</th>
                    <th scope="col">Especialidad</th>
                    <th scope="col">Observaciones</th>
                    <th scope="col">Archivo</th>
                    <th scope="col">Informar</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in records" :key="index">
                    <th scope="row">{{ ++index }}</th>
                    <td>{{ item.student }}</td>
                    <td>{{ item.incident }}</td>
                    <td>{{ item.teacher }}</td>
                    <td>{{ item.specialty }}</td>
                    <td>{{ item.observations }}</td>
                    <td>
                      <a v-if="item.photo" :href="item.photo" target="_blank">
                        Ver
                      </a>
                      <span v-else>No Existe</span>
                    </td>
                    <td>
                      <button
                        class="btn btn-success"
                        @click="sendMessages(item)"
                      >
                        <i class="fa-brands fa-whatsapp"></i>
                      </button>
                      <!-- <a
                        type="button"
                        class="btn btn-success"
                        :href="`https://wa.me/521${
                          item.student_info.relatives.father_data.phone_whatsapp
                        }?text='Estimado Padre de Familia O Tutor, se le solicita su presencia en el Plantel de la escuela para tratar asuntos relacionados con la educación de su hijo, ya que tuvo una incidencia: *${
                          item.incident
                        }*, el día *${item.created_at}*, reportado por Prof. *${
                          item.teacher
                        }* en la especialidad: *${
                          item.specialty
                        }*, observaciones: *${
                          item.observations ?? '-'
                        }*. Lo esperamos lo antes posible en la oficina de Orientación, Archivos que comprueban el caso: ${
                          item.photo
                        }`"
                        target="_blank"
                      >
                        <i class="fa-brands fa-whatsapp"></i>
                      </a> -->
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </form>
      <!-- Modal -->
      <div
        class="modal fade"
        id="modal-photo"
        tabindex="-1"
        aria-labelledby="exampleModalLabel"
        aria-hidden="true"
      >
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="exampleModalLabel">
                Fotografía
              </h1>
              <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
                aria-label="Close"
              ></button>
            </div>
            <div class="modal-body">
              <img style="max-width: 100%" :src="photoLink" alt="" />
            </div>
            <div class="modal-footer">
              <button
                type="button"
                class="btn btn-secondary"
                data-bs-dismiss="modal"
              >
                Close
              </button>
            </div>
          </div>
        </div>
      </div>
    </BaseBlock>
  </div>
</template>

<script setup>
const initialForm = {
  student: null,
};
const form = reactive({ ...initialForm });
const records = ref([]);
const isLoading = ref(false);
const customMessages = ref(false);

onMounted(async () => {
  getConfig();
});

const getConfig = async () => {
  isLoading.value = true;
  try {
    const { data } = await api.get(`/general-configuration`);
    customMessages.value = data.custom_messages;
  } catch (error) {
    console.error(error);
  }
  isLoading.value = false;
};

function sendMessages(item) {
  let message = customMessages.value.incidents;
  message = message.replace("{INCIDENCIA}", item.incident);
  message = message.replace("{FECHA}", item.created_at);
  message = message.replace("{PROFESOR}", item.teacher);
  message = message.replace("{ESPECIALIDAD}", item.specialty);
  message = message.replace("{OBSERVACIONES}", item.observations);
  message = message.replace("{ARCHIVO_URL}", item.photo);

  window.open(
    `https://wa.me/521${item.student_info.home_phone}?text=${message}`,
    "_blank"
  );
}

function submit() {
  isLoading.value = true;

  api
    .get(`incident-reports`, { params: form })
    .then((res) => {
      Toast.fire({
        icon: "success",
        title: "Consulta Correcta",
      });
      records.value = res.data.data;
      isLoading.value = false;
    })
    .catch((err) => {
      console.log(err.response);
      isLoading.value = false;
    });
}

const photoLink = ref();

function modalShow(modalName, data) {
  photoLink.value = data;
  var myModal = new bootstrap.Modal(document.getElementById(modalName));
  myModal.show();
}

const text = ref(
  "Estimado Padre de Familia O Tutor, se le solicita su presencia en el Plantel de la escuela para tratar asuntos relacionados con la educacion de su hijo, ya que tuvo una incidencia: EL ALUMNO(A) NO TRABAJO EN CLASE , el día 09-11-2022 a las 23:08:00 , reportado por Profra. ESPINOSA ROMERO MARIA MARTHA en la especialidad: Sub. Academica , observaciones: dsfdsf dsf dsf sdf, Lo esperamos lo antes posible en la oficina de Orientacion , Archivos que comprueban el caso (http://escuelapresente.com/archivos/chart (12).pdf , )"
);
</script>
