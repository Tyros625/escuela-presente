<script setup>
import moment from "moment";

const isLoading = ref(false);
const enrollment = ref();
const dataFetched = ref();
const amount = ref(40);
const balances = ref();
const currentBalance = ref(0);

function formatDate(date) {
  return moment(date).format("DD/MM/YY");
}

function formatHour(date) {
  return moment(date).format("h:mm a");
}

async function getBalanceByStudent() {
  try {
    const { data } = await api.get(`/balances/${enrollment.value}`);
    console.log(data);
    balances.value = data.data.reverse();
    balances.value.forEach((element) => {
      if (element.type === "income") {
        currentBalance.value += element.amount;
      } else {
        currentBalance.value -= element.amount;
      }
    });
  } catch (error) {
    console.error(error);
  }
}

function searchAssists() {
  if (enrollment.value.length === 7) {
    isLoading.value = true;
    api
      .post("/balances", {
        student_enrollment: enrollment.value,
        amount: amount.value,
        type: "expense",
      })
      .then(async function (res) {
        dataFetched.value = res.data.data;
        Toast.fire({
          icon: "success",
          title: res.data.message,
        });
        await getBalanceByStudent();
        enrollment.value = null;
      })
      .catch(function (error) {
        Toast.fire({
          icon: "error",
          title: error.data.message,
        });
        enrollment.value = null;
      });
  }
}
</script>

<template>
  <!-- Hero -->
  <BasePageHeading title="Asistencia Comedor" />
  <!-- END Hero -->

  <!-- Page Content -->
  <div class="content">
    <BaseBlock title="Asistencia Comedor" content-full>
      <div class="row">
        <div class="col-lg-4">
          <p class="fs-sm text-muted">Ingresar matrícula</p>
        </div>
        <div class="col-lg-8 space-y-2">
          <!-- Form Inline - Default Style -->
          <form class="row row-cols-lg-auto g-3 align-items-center" @sumbit.prevent>
            <div class="col-12">
              <label class="visually-hidden">Email</label>
              <input
                type="text"
                class="form-control"
                placeholder="Matrícula"
                v-model="enrollment"
                @input="searchAssists"
              />
            </div>
          </form>
          <!-- END Form Inline - Default Style -->
        </div>
      </div>
      <div v-if="dataFetched">
        <div class="text-center">
          <h3 class="text-success m-5">
            <b> Se registro la asistencia correctamente </b>
          </h3>
        </div>
        <span><strong>Saldo Actual:</strong>{{ currentBalance }}$</span>
        <div class="container">
          <div class="row">
            <div class="col-sm">
              <h3 class="text-primary">
                <b>Alumno:</b> {{ dataFetched.student.name }}
                {{ dataFetched.student.last_name_father }}
                {{ dataFetched.student.last_name_mother }}
              </h3>
            </div>
            <div class="col-sm">
              <h5><b>Curp:</b> {{ dataFetched.student.curp }}</h5>
            </div>
            <div class="col-sm">
              <h5><b>Grado:</b> {{ dataFetched.student.grade }}</h5>
            </div>
          </div>

          <div class="row">
            <div class="col-sm">
              <h5><b>Grupo:</b> {{ dataFetched.student.group }}</h5>
            </div>
            <div class="col-sm">
              <h5><b>Fecha:</b> {{ formatDate(dataFetched.created_at) }}</h5>
            </div>
            <div class="col-sm">
              <h5><b>Hora:</b> {{ formatHour(dataFetched.created_at) }}</h5>
            </div>
          </div>
        </div>
      </div>
    </BaseBlock>
  </div>
  <!-- END Page Content -->
</template>
