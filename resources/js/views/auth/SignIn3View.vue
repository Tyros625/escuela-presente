<script setup>
import { reactive, computed } from "vue";
import { useTemplateStore } from "@/stores/template";

// Vuelidate, for more info and examples you can check out https://github.com/vuelidate/vuelidate
import useVuelidate from "@vuelidate/core";
import { required, minLength } from "@vuelidate/validators";
import { useUserStore } from "@/stores/user";

// Main store and Router
const store = useTemplateStore();
const router = useRouter();
const userStore = useUserStore();

// Input state variables
const form = reactive({
  email: "",
  password: "",
});

// Validation rules
const rules = computed(() => {
  return {
    email: {
      required,
      minLength: minLength(3),
    },
    password: {
      required,
      minLength: minLength(5),
    },
  };
});

// Use vuelidate
const v$ = useVuelidate(rules, form);

// On form submission
async function onSubmit() {
  const result = await v$.value.$validate();

  if (!result) {
    return Toast.fire({ icon: "error", title: result });
  }

  const res = await userStore.login(form.email, form.password);
  if (res === 200) {
    router.push({ name: "dashboard" });
  }
}
</script>

<template>
  <!-- Page Content -->
  <BaseBackground image="/assets/media/photos/photo28@2x.jpg">
    <div class="row g-0 bg-primary-dark-op">
      <!-- Meta Info Section -->
      <div
        class="hero-static col-lg-4 d-none d-lg-flex flex-column justify-content-center"
      >
        <div class="p-4 p-xl-5 flex-grow-1 d-flex align-items-center">
          <div class="w-100">
            <RouterLink
              :to="{ name: 'landing' }"
              class="link-fx fw-semibold fs-2 text-white"
            >
              Escuela<span class="fw-normal">PRESENTE</span>
            </RouterLink>
            <p class="text-white-75 me-xl-8 mt-2">
              Bienvenido a tu increíble aplicación. Siéntase libre de iniciar sesión y
              comenzar a administrar sus proyectos y clientes.
            </p>
          </div>
        </div>
        <div
          class="p-4 p-xl-5 d-xl-flex justify-content-between align-items-center fs-sm"
        >
          <p class="fw-medium text-white-50 mb-0">
            <strong>{{ store.app.name + " " + store.app.version }}</strong>
            &copy; {{ store.app.copyright }}
          </p>
          <ul class="list list-inline mb-0 py-2">
            <li class="list-inline-item">
              <a class="text-white-75 fw-medium" href="javascript:void(0)">Legal</a>
            </li>
            <li class="list-inline-item">
              <a class="text-white-75 fw-medium" href="javascript:void(0)">Contact</a>
            </li>
            <li class="list-inline-item">
              <a class="text-white-75 fw-medium" href="javascript:void(0)">Terms</a>
            </li>
          </ul>
        </div>
      </div>
      <!-- END Meta Info Section -->

      <!-- Main Section -->
      <div
        class="hero-static col-lg-8 d-flex flex-column align-items-center bg-body-extra-light"
      >
        <div class="p-3 w-100 d-lg-none text-center">
          <RouterLink
            :to="{ name: 'landing' }"
            class="link-fx fw-semibold fs-3 text-dark"
          >
            One<span class="fw-normal">UI</span>
          </RouterLink>
        </div>
        <div class="p-4 w-100 flex-grow-1 d-flex align-items-center">
          <div class="w-100">
            <!-- Header -->
            <div class="text-center mb-5">
              <p class="mb-3">
                <i class="fa fa-2x fa-circle-notch text-primary-light"></i>
              </p>
              <h1 class="fw-bold mb-2">Iniciar Sesión</h1>
              <p class="fw-medium text-muted">Bienvenido, por favor inicie sesión.</p>
            </div>
            <!-- END Header -->

            <!-- Sign In Form -->
            <div class="row g-0 justify-content-center">
              <div class="col-sm-8 col-xl-4">
                <form @submit.prevent="onSubmit">
                  <div class="mb-4">
                    <input
                      type="email"
                      class="form-control form-control-alt form-control-lg"
                      id="login-email"
                      name="login-email"
                      placeholder="Correo Electrónico"
                      :class="{
                        'is-invalid': v$.email.$errors.length,
                      }"
                      v-model="form.email"
                      @blur="v$.email.$touch"
                    />
                    <div
                      v-if="v$.email.$errors.length"
                      class="invalid-feedback animated fadeIn"
                    >
                      Por favor, introduzca su correo electrónico
                    </div>
                  </div>
                  <div class="mb-4">
                    <input
                      type="password"
                      class="form-control form-control-alt form-control-lg"
                      id="login-password"
                      name="login-password"
                      placeholder="Contraseña"
                      :class="{
                        'is-invalid': v$.password.$errors.length,
                      }"
                      v-model="form.password"
                      @blur="v$.password.$touch"
                    />
                    <div
                      v-if="v$.password.$errors.length"
                      class="invalid-feedback animated fadeIn"
                    >
                      Por favor, introduzca su contraseña
                    </div>
                  </div>
                  <div class="d-flex justify-content-between align-items-center mb-4">
                    <div></div>
                    <div>
                      <button type="submit" class="btn btn-lg btn-alt-primary">
                        <i class="fa fa-fw fa-sign-in-alt me-1 opacity-50"></i>
                        Iniciar Sesión
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
            <!-- END Sign In Form -->
          </div>
        </div>
        <div
          class="px-4 py-3 w-100 d-lg-none d-flex flex-column flex-sm-row justify-content-between fs-sm text-center text-sm-start"
        >
          <p class="fw-medium text-black-50 py-2 mb-0">
            <strong>{{ store.app.name + " " + store.app.version }}</strong>
            &copy; {{ store.app.copyright }}
          </p>
          <ul class="list list-inline py-2 mb-0">
            <li class="list-inline-item">
              <a class="text-muted fw-medium" href="javascript:void(0)">Legal</a>
            </li>
            <li class="list-inline-item">
              <a class="text-muted fw-medium" href="javascript:void(0)">Contact</a>
            </li>
            <li class="list-inline-item">
              <a class="text-muted fw-medium" href="javascript:void(0)">Terms</a>
            </li>
          </ul>
        </div>
      </div>
      <!-- END Main Section -->
    </div>
  </BaseBackground>
  <!-- END Page Content -->
</template>
