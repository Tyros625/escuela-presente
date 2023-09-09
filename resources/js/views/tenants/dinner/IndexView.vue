<script setup>
import moment from 'moment';

const isLoading = ref(false)
const enrollment = ref()
const dataFetched = ref()
const amount = ref(0)
const recharge = ref(false)
const balances = ref([])
const currentBalance = ref(0)

function formatDateHour(date) {
  return moment(date).format("DD/MM/YY h:mm a");
}

async function getBalanceByStudent() {
  try {
    const { data } = await api.get(`/balances/${enrollment.value}`);
    console.log(data);
    balances.value = data.data.reverse()
    balances.value.forEach(element => {
      if (element.type === 'income') {
        currentBalance.value += element.amount
      } else {
        currentBalance.value -= element.amount
      }
    });
  } catch (error) {
    console.error(error);
  }
}

function topUpBalance() {
  if (amount.value <= 0) {
    return Toast.fire({
      icon: "error",
      title: 'El monto debe ser mayor a 0',
    });
  }

  api.post('/balances', {
    student_enrollment: dataFetched.value.enrollment,
    amount: amount.value,
    type: "income"
  })
    .then(async function (res) {
      Toast.fire({
        icon: "success",
        title: res.data.message,
      });
      await getBalanceByStudent()
    })
    .catch(function (error) {
      console.log(error);
    });
}

const stringLength = computed(() => enrollment.value.toString().length)

function searchStudent() {
  if (stringLength.value === 7) {
    isLoading.value = true;
    api
      .get(`/students/${enrollment.value}`)
      .then((res) => {
        dataFetched.value = res.data.data
        Toast.fire({
          icon: "success",
          title: res.data.message,
        });
        isLoading.value = false
      })
      .catch((err) => {
        Toast.fire({
          icon: "error",
          title: err.data.message,
        });
        isLoading.value = false
      });

  }
}
</script>

<template>
  <!-- Hero -->
  <BasePageHeading title="Comedor Escolar" />
  <!-- END Hero -->

  <!-- Page Content -->
  <div class="content">
    <BaseBlock title="Comedor Escolar" content-full>
      <div class="row">
        <div class="col-lg-4">
          <p class="fs-sm text-muted">
            Ingresar matrícula
          </p>
        </div>
        <div class="col-lg-8 space-y-2">
          <!-- Form Inline - Default Style -->
          <form class="row row-cols-lg-auto g-3 align-items-center">
            <div class="col-12">
              <label class="visually-hidden">Email</label>
              <input type="text" class="form-control" placeholder="Matrícula" v-model="enrollment"
                @input="searchStudent" />
            </div>
          </form>
          <!-- END Form Inline - Default Style -->
        </div>
      </div>
      <div v-if="dataFetched">
        <div class="container mt-5" v-if="!recharge && balances.length === 0">
          <table class="table">
            <thead>
              <tr class="table-primary">
                <th scope="col">Matricula</th>
                <th scope="col">Nombre</th>
                <th scope="col">Apellido Materno</th>
                <th scope="col">Apellido Paterno</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <th scope="row">{{ dataFetched.enrollment }}</th>
                <td>{{ dataFetched.name }}</td>
                <td>{{ dataFetched.last_name_father }}</td>
                <td>{{ dataFetched.last_name_mother }}</td>
              </tr>
            </tbody>
          </table>

          <a class="btn btn-primary" @click="recharge = true">Recargar Saldo</a>
          <a class="btn btn-primary m-2" @click="getBalanceByStudent">Información General</a>

        </div>

        <div class="container mt-5" v-if="recharge && balances.length === 0">

          <div class="text-center">
            <h3 class="text-dark">
              Alumno: "<b>{{ `${dataFetched.name} ${dataFetched.last_name_father}
                              ${dataFetched.last_name_mother}`
              }}</b>"
            </h3>
            <span>Matricula = <b>{{ dataFetched.enrollment }}</b></span>
          </div>

          <div class="text-center">
            <div class="form-group col-md-4">
              <label>Monto $</label>
              <input type="number" class="form-control" placeholder="Ingrese Saldo" step="any" v-model="amount">
            </div>
            <button type="submit" class="btn btn-primary mt-2" @click="topUpBalance">Recargar</button>
          </div>

        </div>

        <div class="container mt-5" v-if="balances.length > 0">

          <div class="text-center">
            <h3 class="text-dark">
              Alumno: "<b>{{ `${dataFetched.name} ${dataFetched.last_name_father}
                              ${dataFetched.last_name_mother}`
              }}</b>"
            </h3>
            <span>Matricula = <b>{{ dataFetched.enrollment }}</b></span>
          </div>

          <h3 class="text-primary">Saldo Actual: {{ currentBalance }} $</h3>
          <table class="table table-success">
            <thead class="thead-dark">
              <tr>
                <th scope="col">SALDO</th>
                <th scope="col">TIPO</th>
                <th scope="col">FECHA</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in balances" :key="item">
                <th scope="row" class="text-success" v-if="item.type === 'income'">{{ `+${item.amount}` }} $</th>
                <th scope="row" class="text-success" v-else>{{ `-${item.amount}` }} $</th>
                <td class="text-success"><b>{{ item.type === 'income' ? 'Ingreso' : 'Egreso' }}</b></td>
                <td class="text-success"><b>{{ formatDateHour(item.created_at) }} </b></td>
              </tr>
            </tbody>
          </table>

        </div>

      </div>

    </BaseBlock>
  </div>
  <!-- END Page Content -->
</template>
