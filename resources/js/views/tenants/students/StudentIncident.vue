<script setup>
const route = useRoute();
const records = ref([]);

onMounted(async () => {
  console.log("Route Name: " + route.name);
  console.log(route.params.id);
  getData();
});

const getData = async () => {
  const { data } = await api.get(`/incident-reports/${route.params.id}`);
  records.value = data.data.reverse();
};

const photoLink = ref();

function modalShow(modalName, data) {
  photoLink.value = data;
  var myModal = new bootstrap.Modal(document.getElementById(modalName));
  myModal.show();
}
</script>

<template>
  <!-- Hero -->
  <BasePageHeading :title="`Incidente Estudiante #${route.params.id}`" />
  <!-- END Hero -->

  <!-- Page Content -->
  <div class="content">
    <BaseBlock title="Incidentes de Estudiantes">
      <template #options>
        <button type="button" class="btn-block-option">
          <i class="si si-settings"></i>
        </button>
      </template>

      <div class="col-md-12 mt-3">
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Fecha</th>
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
                <td>
                  {{ `${item.created_at}` }}
                </td>
                <td>{{ item.incident.description }}</td>
                <td>{{ `${item.teacher.last_name}, ${item.teacher.name}` }}</td>
                <td>{{ item.specialty.description }}</td>
                <td>{{ item.observations }}</td>
                <td>
                  <a v-if="item.photo" :href="item.photo" target="_blank"
                    >Ver</a
                  >
                  <span v-else>No Existe</span>
                </td>
                <td>
                  <a
                    type="button"
                    class="btn btn-success"
                    :href="`https://wa.me/521${
                      item.student.relative_data?.father_data?.phone_whatsapp
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
                  </a>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </BaseBlock>
  </div>
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
          <h1 class="modal-title fs-5" id="exampleModalLabel">Fotografía</h1>
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
  <!-- END Page Content -->
</template>
