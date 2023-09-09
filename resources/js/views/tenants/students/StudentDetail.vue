<script setup>
const route = useRoute();
const records = ref([]);

onMounted(async () => {
  console.log("Route Name: " + route.name);
  console.log(route.params.id);
  getData();
});

const getData = async () => {
  const { data } = await api.get(`/students/${route.params.id}`);
  records.value = data.data;
};

const supersede = function (val) {
  console.log(val);
};

const socioeconomics = computed(() => {
  try {
    return Object.keys(records.value.socioeconomics.ownerships)
      .filter((k) => records.value.socioeconomics.ownerships[k])
      .join(", ")
      .toUpperCase()
      .replace("KIND_HOUSE", "CASA PROPIA")
      .replace("AGUA_DRENAJE", "AGUA Y DRENAJE")
      .replace("BAÑO_PROPIO", "BAÑO PROPIO");
  } catch (e) {
    return "-";
  }
});

const breakfast = computed(() => {
  try {
    return Object.keys(records.value.socioeconomics.nutrition.breakfast)
      .filter((k) => records.value.socioeconomics.nutrition.breakfast[k])
      .join(", ")
      .toUpperCase()
      .replace("EGG", "HUEVOS")
      .replace("MILK", "LECHE")
      .replace("BREAD", "PAN")
      .replace("FRUIT", "FRUTA")
      .replace("FAST_FOOD", "COMIDA CHATARRA");
  } catch (e) {
    return "-";
  }
});

const lunch = computed(() => {
  try {
    return Object.keys(records.value.socioeconomics.nutrition.lunch)
      .filter((k) => records.value.socioeconomics.nutrition.lunch[k])
      .join(", ")
      .toUpperCase()
      .replace("FISH", "PESCADO")
      .replace("MEAT", "CARNE")
      .replace("FLOUR", "HARINAS")
      .replace("FRUIT", "FRUTA")
      .replace("FAST_FOOD", "COMIDA CHATARRA")
      .replace("VEGETABLE", "VEGETALES");
  } catch (e) {
    return "-";
  }
});

const dinner = computed(() => {
  try {
    return Object.keys(records.value.socioeconomics.nutrition.dinner)
      .filter((k) => records.value.socioeconomics.nutrition.dinner[k])
      .join(", ")
      .toUpperCase()
      .replace("EGG", "HUEVOS")
      .replace("MEAT", "CARNE")
      .replace("MILK", "LECHE")
      .replace("FLOUR", "HARINAS")
      .replace("COFFEE", "CAFE")
      .replace("FAST_FOOD", "COMIDA CHATARRA");
  } catch (e) {
    return "-";
  }
});

const familiarAffection = computed(() => {
  try {
    return Object.keys(records.value.healths.familiar_affection)
      .filter((k) => records.value.healths.familiar_affection[k])
      .join(", ")
      .toUpperCase()
      .replace("DOPE", "MARIHUANA")
      .replace("CIGAR", "CIGARRO")
      .replace("DRUGS", "DROGAS")
      .replace("COFFEE", "CAFE")
      .replace("NERVE_PILLS", "PASTILLAS PARA LOS NERVIOS")
      .replace("SLEEPING_PILLS", "PASTILLAS PARA DORMIR")
      .replace("ALCOHOLIC_DRINKS", "BEBIDAS ALCOHOLICAS");
  } catch (e) {
    return "-";
  }
});

const medicalCare = computed(() => {
  try {
    return Object.keys(records.value.healths.medical_care)
      .filter((k) => records.value.healths.medical_care[k])
      .join(", ")
      .toUpperCase()
      .replace("GLASSES", "LENTES")
      .replace("TONSILS", "AMIGDALAS")
      .replace("FLATFOOT", "PIE PLANO")
      .replace("SEIZURES", "CONVULSIONES")
      .replace("ACCIDENTS", "ACCIDENTES")
      .replace("MEDICINES", "MEDICINAS")
      .replace("OPERATIONS", "OPERACIONES")
      .replace("HEARING_PROBLEMS", "PROBLEMAS AUDITIVOS")
      .replace("ORTHOPEDIC_DEVICE", "APARATO ORTOPEDICO")
      .replace("COMPLETE_VACCINATIONS", "VACUNAS COMPLETAS");
  } catch (e) {
    return "-";
  }
});
</script>

<template>
  <!-- Hero -->
  <BasePageHeading :title="`Estudiante #${route.params.id}`" />
  <!-- END Hero -->

  <!-- Page Content -->
  <div class="content">
    <BaseBlock title="Ficha de Estudiante">
      <template #options>
        <button type="button" class="btn-block-option">
          <i class="si si-settings"></i>
        </button>
      </template>

      <div class="container">
        <h5>Datos Personales</h5>
        <button
          type="button"
          class="btn btn-primary"
          data-bs-toggle="modal"
          data-bs-target="#exampleModal"
        >
          Ver Fotografía
        </button>
        <div class="table-responsive">
          <table id="student" class="table">
            <tr>
              <td class="paint">Apellido Paterno</td>
              <td class="paint">Apellido Materno</td>
              <td class="paint">Nombres</td>
              <td class="paint">Nacionalidad</td>
            </tr>
            <tr>
              <td>{{ records.last_name_father }}</td>
              <td>{{ records.last_name_mother }}</td>
              <td>{{ records.name }}</td>
              <td>{{ records.nationality }}</td>
            </tr>
            <tr>
              <td class="paint">CURP</td>
              <td class="paint">Fecha de Nacimiento</td>
              <td class="paint">Lugar de Nacimiento</td>
              <td class="paint">Sexo</td>
            </tr>
            <tr>
              <td>{{ records.curp }}</td>
              <td>{{ records.date_birth }}</td>
              <td>{{ records.place_birth }}</td>
              <td>{{ records.sex }}</td>
            </tr>
            <tr>
              <td class="paint">Peso</td>
              <td class="paint">Estatura</td>
              <td class="paint">Migrante</td>
              <td class="paint">Grupo Indigena</td>
            </tr>
            <tr>
              <td>{{ records.weight }}</td>
              <td>{{ records.height }}</td>
              <td>{{ records.is_migrant ? "SI" : "NO" }}</td>
              <td>{{ records.indigenous_group }}</td>
            </tr>
            <tr>
              <td class="paint">Lengua Indigena</td>
              <td class="paint">Discapacidad</td>
              <td class="paint">Cuenta con Seguro Medico</td>
              <td class="paint">Cuenta con Beca</td>
            </tr>
            <tr>
              <td>{{ records.indigenous_language }}</td>
              <td>{{ records.disability }}</td>
              <td>{{ records.health_insurance }}</td>
              <td>{{ records.scholarship }}</td>
            </tr>
            <tr>
              <td class="paint">Domicilio Calle</td>
              <td class="paint">Colonia</td>
              <td class="paint">Código Postal</td>
              <td class="paint">Delegación o Municipio</td>
            </tr>
            <tr>
              <td>{{ records.address }}</td>
              <td>{{ records.colony }}</td>
              <td>{{ records.postal_code }}</td>
              <td>{{ records.municipality }}</td>
            </tr>
            <tr>
              <td class="paint" colspan="2">Entidad Federativa</td>
              <td class="paint">Telefono de Casa</td>
              <td class="paint">Correo Electrónico</td>
            </tr>
            <tr>
              <td colspan="2">{{ records.federal_entity }}</td>
              <td>{{ records.home_phone }}</td>
              <td>{{ records.email ?? "-" }}</td>
            </tr>
          </table>
        </div>
        <hr />
        <h5>Datos Académicos</h5>
        <div class="table-responsive">
          <table id="student" class="table">
            <tr>
              <td class="paint">Grado</td>
              <td class="paint">Grupo</td>
              <td class="paint">Ciclo Escolar</td>
            </tr>
            <tr>
              <td>{{ records?.grade }}</td>
              <td>{{ records?.group }}</td>
              <td>{{ records?.school_cycle }}</td>
            </tr>
            <tr>
              <td class="paint">Educación Especial UDEEI</td>
              <td class="paint">Escuela de Procedencia</td>
              <td class="paint">Entidad Federativa de la Escuela</td>
            </tr>
            <tr>
              <td>{{ records.academic?.udeei }}</td>
              <td>{{ records.academic?.origin_school }}</td>
              <td>{{ records.academic?.federal_entity_school }}</td>
            </tr>
          </table>
        </div>

        <hr />
        <h5>Datos Familiares</h5>
        <div class="table-responsive">
          <table id="student" class="table">
            <tr>
              <td class="paint" colspan="4">Datos del Padre o Tutor</td>
            </tr>
            <tr>
              <td class="paint">Nombres</td>
              <td class="paint">Parentesco</td>
              <td class="paint">Ocupación</td>
              <td class="paint">Teléfono</td>
            </tr>
            <tr>
              <td>{{ records.relatives?.father_data?.name }}</td>
              <td>{{ records.relatives?.father_data?.relationship }}</td>
              <td>{{ records.relatives?.father_data?.occupation }}</td>
              <td>{{ records.relatives?.father_data?.phone_whatsapp }}</td>
            </tr>
            <tr>
              <td class="paint" colspan="2">Domicilio del Trabajo</td>
              <td class="paint">Teléfono del Trabajo</td>
              <td class="paint">Correo Electrónico</td>
            </tr>
            <tr>
              <td colspan="2">{{ records.relatives?.father_data?.work_address }}</td>
              <td>{{ records.relatives?.father_data?.work_phone }}</td>
              <td>{{ records.relatives?.father_data?.email }}</td>
            </tr>
            <tr>
              <td class="paint" colspan="4">Datos de la Madre o Tutora</td>
            </tr>
            <tr>
              <td class="paint">Nombres</td>
              <td class="paint">Parentesco</td>
              <td class="paint">Ocupación</td>
              <td class="paint">Teléfono</td>
            </tr>
            <tr>
              <td>{{ records.relatives?.mother_data?.name }}</td>
              <td>{{ records.relatives?.mother_data?.relationship }}</td>
              <td>{{ records.relatives?.mother_data?.occupation }}</td>
              <td>{{ records.relatives?.mother_data?.phone_whatsapp }}</td>
            </tr>
            <tr>
              <td class="paint" colspan="2">Domicilio del Trabajo</td>
              <td class="paint">Teléfono del Trabajo</td>
              <td class="paint">Correo Electrónico</td>
            </tr>
            <tr>
              <td colspan="2">{{ records.relatives?.mother_data?.work_address }}</td>
              <td>{{ records.relatives?.mother_data?.work_phone }}</td>
              <td>{{ records.relatives?.mother_data?.email }}</td>
            </tr>
            <tr>
              <td class="paint" colspan="4">
                Personas Autorizadas Para Recoger Al Alumno en el Plantel
              </td>
            </tr>
            <tr>
              <td class="paint" colspan="2">Persona 1</td>
              <td class="paint" colspan="2">Parentesco</td>
            </tr>
            <tr>
              <td colspan="2">{{ records.relatives?.authorized_persons[0]?.name }}</td>
              <td colspan="2">
                {{ records.relatives?.authorized_persons[0]?.relationship }}
              </td>
            </tr>
            <tr>
              <td class="paint" colspan="2">Persona 2</td>
              <td class="paint" colspan="2">Parentesco</td>
            </tr>
            <tr>
              <td colspan="2">{{ records.relatives?.authorized_persons[1]?.name }}</td>
              <td colspan="2">
                {{ records.relatives?.authorized_persons[1]?.relationship }}
              </td>
            </tr>
            <tr>
              <td class="paint" colspan="2">Persona 3</td>
              <td class="paint" colspan="2">Parentesco</td>
            </tr>
            <tr>
              <td colspan="2">
                {{ records.relatives?.authorized_persons[2]?.name ?? "-" }}
              </td>
              <td colspan="2">
                {{ records.relatives?.authorized_persons[2]?.relationship }}
              </td>
            </tr>
            <tr>
              <td class="paint" colspan="4">
                Hermanos o Familiares que Vivan en el Mismo Domicilio del Alumno
              </td>
            </tr>
            <tr>
              <td class="paint">Persona 1</td>
              <td class="paint">Edad</td>
              <td class="paint">Sexo</td>
              <td class="paint">Parentesco</td>
            </tr>
            <tr>
              <td>{{ records.relatives?.roommates[0]?.name ?? "-" }}</td>
              <td>{{ records.relatives?.roommates[0]?.age ?? "-" }}</td>
              <td>{{ records.relatives?.roommates[0]?.sex ?? "-" }}</td>
              <td>{{ records.relatives?.roommates[0]?.relationship ?? "-" }}</td>
            </tr>
            <tr>
              <td class="paint">Persona 2</td>
              <td class="paint">Edad</td>
              <td class="paint">Sexo</td>
              <td class="paint">Parentesco</td>
            </tr>
            <tr>
              <td>{{ records.relatives?.roommates[1]?.name ?? "-" }}</td>
              <td>{{ records.relatives?.roommates[1]?.age ?? "-" }}</td>
              <td>{{ records.relatives?.roommates[1]?.sex ?? "-" }}</td>
              <td>{{ records.relatives?.roommates[1]?.relationship ?? "-" }}</td>
            </tr>
            <tr>
              <td class="paint">Persona 3</td>
              <td class="paint">Edad</td>
              <td class="paint">Sexo</td>
              <td class="paint">Parentesco</td>
            </tr>
            <tr>
              <td>{{ records.relatives?.roommates[2]?.name ?? "-" }}</td>
              <td>{{ records.relatives?.roommates[2]?.age ?? "-" }}</td>
              <td>{{ records.relatives?.roommates[2]?.sex ?? "-" }}</td>
              <td>{{ records.relatives?.roommates[2]?.relationship ?? "-" }}</td>
            </tr>
          </table>
        </div>
        <hr />
        <h5>Datos Socioeconómicos</h5>
        <div class="table-responsive">
          <table id="student" class="table">
            <tr>
              <td class="paint" colspan="4">El Alumno Cuenta con</td>
            </tr>
            <tr>
              <td colspan="4">{{ socioeconomics }}</td>
            </tr>
            <tr>
              <td class="paint" colspan="4">Nutrición</td>
            </tr>
            <tr>
              <td colspan="4">
                <b>Desayuno:</b> {{ breakfast }} <br />
                <b>Almuerzo:</b> {{ lunch }} <br />
                <b>Cena:</b> {{ dinner }}
              </td>
            </tr>
          </table>
        </div>
        <hr />
        <h5>Datos de Salud e Higiene</h5>
        <div class="table-responsive">
          <table id="student">
            <tr>
              <td class="paint">Estado de Salud</td>
              <td class="paint">Tipo de Sangre</td>
              <td class="paint">Enfermedad Crónica</td>
              <td class="paint">¿Tiene Servicio Médico?</td>
            </tr>
            <tr>
              <td>{{ records.healths?.current_general_status ?? "-" }}</td>
              <td>{{ records.healths?.blood_type ?? "-" }}</td>
              <td>{{ records.healths?.chronic_disease ?? "-" }}</td>
              <td>{{ records.healths?.has_medical_service ? "SI" : "NO" }}</td>
            </tr>
            <tr>
              <td class="paint" colspan="2">Número de Afiliación</td>
              <td class="paint" colspan="2">Nombre</td>
            </tr>
            <tr>
              <td colspan="2">{{ records.healths?.medical_service_number ?? "-" }}</td>
              <td colspan="2">{{ records.healths?.medical_service_name ?? "-" }}</td>
            </tr>
            <tr>
              <td class="paint" colspan="2">Familiares Afectos</td>
              <td class="paint" colspan="2">Características</td>
            </tr>
            <tr>
              <td colspan="2">{{ familiarAffection }}</td>
              <td colspan="2">{{ medicalCare }}</td>
            </tr>
          </table>
        </div>
      </div>
    </BaseBlock>
  </div>
  <!-- END Page Content -->

  <!-- Modal -->
  <div
    class="modal fade"
    id="exampleModal"
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
          <img style="max-width: 100%" :src="records.photo" alt="" />
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            Cerrar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
#student {
  border-collapse: collapse;
  width: 100%;
  table-layout: fixed;
}

#student td,
#student th {
  border: 2px solid #fff;
  padding: 5px;
}

#student td.paint {
  padding-top: 10px;
  padding-bottom: 10px;
  text-align: left;
  background-color: #232e3e;
  color: white;
  font-weight: bold;
}
</style>
