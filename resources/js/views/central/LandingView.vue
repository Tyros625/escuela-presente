<script setup>
import { useTemplateStore } from "@/stores/template";
import Swal from "sweetalert2";

// Main store
const store = useTemplateStore();

const initialForm = {
  cct: null,
  school_name: null,
  domain: "",
  email: null,
  password: null,
  password_confirmation: null,
  country_code: 52,
  phone: null,
};
const form = reactive({ ...initialForm });

const options = [
  {
    label: "México",
    value: 52,
  },
  {
    label: "Perú",
    value: 51,
  },
];

function onSubmit() {
  api
    .post(`/tenants/public`, form)
    .then(() => {
      Swal.fire(
        "¡Excelente!",
        "Se le enviará un correo electrónico con los datos de acceso al sistema.",
        "success"
      );

      Object.assign(form, initialForm);
    })
    .catch((err) => {
      console.log(err);
      Toast.fire({
        icon: "error",
        title: "Error",
      });
    });
}

watch(
  () => form.school_name,
  (school_name, prevCount) => {
    form.domain = `${slugify(school_name)}`;
    domain.value = `${slugify(school_name)}.${import.meta.env.VITE_APP_NAME}`;
  }
);

const domain = ref();

const slugify = (str) =>
  str
    .toString()
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .toLowerCase()
    .trim()
    .replace(/\s+/g, "-")
    .replace(/[^\w-]+/g, "")
    .replace(/--+/g, "-");
</script>

<template>
  <div>
    <!-- Full-screen hero with background + registration form on side -->
    <div class="central-landing-hero">
      <div class="central-landing-hero__overlay"></div>
      <div class="central-landing-hero__content">
        <div class="central-landing-hero__container">
          <div class="row align-items-center g-4 py-5">
          <!-- Left: registration form card -->
          <div class="col-lg-5 col-xl-4">
            <div class="central-landing-form-card">
              <div class="central-landing-form-card__header">
                <h2 class="central-landing-form-card__title">Crear cuenta gratuita</h2>
                <p class="central-landing-form-card__subtitle">Complete los datos de su institución</p>
              </div>
              <div class="central-landing-form-card__body">
                <form @submit.prevent="onSubmit" class="central-landing-form">
                  <div class="form-floating central-landing-form__field">
                    <input
                      type="text"
                      class="form-control"
                      :class="{ 'is-invalid': !form.cct }"
                      v-model="form.cct"
                      placeholder=" "
                    />
                    <label>CCT</label>
                  </div>
                  <div class="form-floating central-landing-form__field">
                    <input
                      type="text"
                      class="form-control"
                      :class="{ 'is-invalid': !form.school_name }"
                      v-model="form.school_name"
                      placeholder=" "
                    />
                    <label>¿Cómo se llama su colegio?</label>
                  </div>
                  <div class="form-floating central-landing-form__field">
                    <input
                      type="text"
                      class="form-control"
                      :class="{ 'is-invalid': !domain }"
                      v-model="domain"
                      readonly
                      placeholder=" "
                    />
                    <label>Su dominio será:</label>
                  </div>
                  <div class="form-floating central-landing-form__field">
                    <input
                      type="email"
                      class="form-control"
                      :class="{ 'is-invalid': !form.email }"
                      v-model="form.email"
                      placeholder=" "
                    />
                    <label>Correo Electrónico</label>
                  </div>
                  <div class="central-landing-form__row">
                    <div class="form-floating central-landing-form__field">
                      <input
                        type="password"
                        class="form-control"
                        :class="{ 'is-invalid': !form.password }"
                        v-model="form.password"
                        placeholder=" "
                      />
                      <label>Contraseña</label>
                    </div>
                    <div class="form-floating central-landing-form__field">
                      <input
                        type="password"
                        class="form-control"
                        :class="{ 'is-invalid': !form.password_confirmation }"
                        v-model="form.password_confirmation"
                        placeholder=" "
                      />
                      <label>Confirmar</label>
                    </div>
                  </div>
                  <div class="form-floating central-landing-form__field">
                    <select
                      class="form-select"
                      :class="{ 'is-invalid': !form.country_code }"
                      v-model="form.country_code"
                    >
                      <option v-for="item in options" :key="item.value" :value="item.value">
                        {{ `(+${item.value}) ${item.label}` }}
                      </option>
                    </select>
                    <label>País/Country</label>
                  </div>
                  <div class="form-floating central-landing-form__field">
                    <input
                      type="tel"
                      class="form-control"
                      :class="{ 'is-invalid': !form.phone }"
                      v-model="form.phone"
                      placeholder=" "
                    />
                    <label>Número de WhatsApp</label>
                  </div>
                  <button
                    type="submit"
                    class="btn central-landing-form__submit"
                    :disabled="
                      !form.cct ||
                      !form.school_name ||
                      !form.email ||
                      !form.password ||
                      !form.phone
                    "
                  >
                    <i class="fa-solid fa-user-plus me-2"></i>
                    Crear Cuenta Gratuita
                  </button>
                </form>
              </div>
            </div>
          </div>
          <!-- Right: hero text -->
          <div class="col-lg-6 col-xl-6 offset-lg-1 offset-xl-1 central-landing-hero__text">
            <div class="text-center text-lg-start hero-text-offset">
              <div class="central-landing-hero__badge mb-3">
                <i class="fa-solid fa-cloud me-2"></i>
                <span>Plataforma multitenant</span>
              </div>
              <h1 class="central-landing-hero__title mb-3">
                Sistema de Gestión Escolar en la Nube
              </h1>
              <p class="central-landing-hero__subtitle mb-0">
                Crea una cuenta gratuita y gestiona tu institución educativa de forma segura.
              </p>
            </div>
          </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Hero -->
    <div id="one-vue-hero" class="bg-body-extra-light">
      <div class="content content-full">
        <div class="row g-0 justify-content-center text-center">
          <div class="col-md-10 pt-7 pb-9">
            <div
              class="d-inline-flex align-items-center space-x-1 fs-sm badge bg-danger-light text-danger mb-2 p-2"
            >
              <i class="fab fa-fw fa-laravel"></i>
              <span>Laravel 9 (with Vite) version is here!</span>
            </div>
            <h1 class="fs-2 fw-bold mb-3">
              Build web apps that your users will love using
            </h1>
            <p class="fs-5 fw-medium text-muted mb-5 mx-xl-8">
              One super flexible UI framework for amazing developers and web agencies. Now
              based on
              <span class="text-body-color fw-semibold">Vue.js 3</span>, with
              <span class="text-body-color fw-semibold">OneUI 5 design</span>
              and <span class="text-body-color fw-semibold">Bootstrap 5</span>
              in core. Comes packed with brand new libraries and tooling, including
              <span class="text-body-color fw-semibold">Vite</span>,
              <span class="text-body-color fw-semibold">Pinia</span> and
              <span class="text-body-color fw-semibold">Composition API</span>.
            </p>
            <RouterLink
              :to="{ name: 'dashboard' }"
              class="btn btn-primary py-2 px-3 m-1"
              v-click-ripple
            >
              <i class="fa fa-fw fa-desktop opacity-50 me-1"></i> Live preview
            </RouterLink>
            <a
              class="btn btn-alt-primary py-2 px-3 m-1"
              href="https://1.envato.market/AVD6j"
              v-click-ripple
            >
              <i class="fa fa-fw fa-link opacity-50"></i>
              <span class="ms-2">OneUI Package</span>
            </a>
          </div>
        </div>
      </div>
    </div>
    <!-- END Hero -->

    <!-- Hero After -->
    <div id="one-vue-hero-after" class="bg-body-light">
      <div class="content content-full">
        <div class="px-lg-8 text-center">
          <BaseBlock
            fx-shadow
            class="overflow-hidden"
            content-class="p-2"
            style="margin-top: -200px"
          >
            <img
              class="img-fluid"
              src="/assets/media/various/hero-promo.png"
              alt="Hero Promo Light Dashboard"
            />
          </BaseBlock>
        </div>
        <div class="row py-5">
          <div class="col-6 col-md-3">
            <div class="item item-rounded my-4 text-flat bg-flat-lighter">
              <i class="fab fa-fw fa-2x fa-vuejs"></i>
            </div>
            <h4 class="mb-2">Vue.js 3</h4>
            <p class="text-muted">
              The latest version of the progressive JavaScript framework is now in core.
            </p>
          </div>
          <div class="col-6 col-md-3">
            <div class="item item-rounded my-4 text-danger bg-danger-light">
              <i class="fab fa-fw fa-2x fa-laravel"></i>
            </div>
            <h4 class="mb-2">Laravel 9</h4>
            <p class="text-muted">
              A brand new Laravel 9 with Vite integration version is now included.
            </p>
          </div>
          <div class="col-6 col-md-3">
            <div class="item item-rounded my-4 text-default bg-default-lighter">
              <i class="fa fa-fw fa-2x fa-circle-notch"></i>
            </div>
            <h4 class="mb-2">OneUI 5</h4>
            <p class="text-muted">
              Based on the design of our best seller dashboard template.
            </p>
          </div>
          <div class="col-6 col-md-3">
            <div class="item item-rounded my-4 text-amethyst bg-amethyst-lighter">
              <i class="fab fa-fw fa-2x fa-bootstrap"></i>
            </div>
            <h4 class="mb-2">Bootstrap 5</h4>
            <p class="text-muted">
              The latest and greatest framework version under the hood.
            </p>
          </div>
        </div>
      </div>
    </div>
    <!-- END Hero After -->

    <!-- Versions -->
    <div id="one-vue-versions" class="bg-body-extra-light">
      <div class="content content-full">
        <div class="py-5">
          <div class="row mb-5">
            <div class="col-md-6">
              <h2 class="h1 fw-bold mb-2">
                Precios
                <span class="fw-normal">Versions</span>
              </h2>
              <p class="fs-lg fw-medium text-muted mb-0">
                Either you are building a pure API powered Vue.js app or a Laravel Vue.js
                based one, we've got you covered.
              </p>
            </div>
            <div
              class="col-md-6 d-none d-md-flex align-items-md-center justify-content-md-end"
            >
              <p class="h1 fw-bold text-body-bg-dark mb-0">Fully Loaded.</p>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 col-xl-3">
            <!-- Developer Plan -->
            <BaseBlock tag="a" href="javascript:void(0)" class="text-center" link-shadow>
              <template #content>
                <div class="block-header">
                  <h3 class="block-title">Developer</h3>
                </div>
                <div class="block-content bg-body-light">
                  <div class="py-2">
                    <p class="h1 fw-bold mb-2">$9</p>
                    <p class="h6 text-muted">per month</p>
                  </div>
                </div>
                <div class="block-content">
                  <div class="fs-sm py-2">
                    <p><strong>2</strong> Projects</p>
                    <p><strong>10GB</strong> Storage</p>
                    <p><strong>15</strong> Clients</p>
                    <p><strong>Email</strong> Support</p>
                  </div>
                </div>
                <div class="block-content block-content-full bg-body-light">
                  <span class="btn btn-secondary px-4">Sign Up</span>
                </div>
              </template>
            </BaseBlock>
            <!-- END Developer Plan -->
          </div>
          <div class="col-md-6 col-xl-3">
            <!-- Startup Plan -->
            <BaseBlock tag="a" href="javascript:void(0)" class="text-center" link-shadow>
              <template #content>
                <div class="block-header">
                  <h3 class="block-title">Startup</h3>
                </div>
                <div class="block-content bg-body-light">
                  <div class="py-2">
                    <p class="h1 fw-bold mb-2">$29</p>
                    <p class="h6 text-muted">per month</p>
                  </div>
                </div>
                <div class="block-content">
                  <div class="fs-sm py-2">
                    <p><strong>10</strong> Projects</p>
                    <p><strong>30GB</strong> Storage</p>
                    <p><strong>100</strong> Clients</p>
                    <p><strong>FULL</strong> Support</p>
                  </div>
                </div>
                <div class="block-content block-content-full bg-body-light">
                  <span class="btn btn-secondary px-4">Sign Up</span>
                </div>
              </template>
            </BaseBlock>
            <!-- END Startup Plan -->
          </div>
          <div class="col-md-6 col-xl-3">
            <!-- Business Plan -->
            <BaseBlock
              tag="a"
              href="javascript:void(0)"
              class="text-center"
              link-shadow
              themed
              fx-shadow
            >
              <template #content>
                <div class="block-header">
                  <h3 class="block-title">
                    <i class="fa fa-thumbs-up me-1"></i> Business
                  </h3>
                </div>
                <div class="block-content bg-body-light">
                  <div class="py-2">
                    <p class="h1 fw-bold mb-2">$49</p>
                    <p class="h6 text-muted">per month</p>
                  </div>
                </div>
                <div class="block-content">
                  <div class="fs-sm py-2">
                    <p><strong>50</strong> Projects</p>
                    <p><strong>100GB</strong> Storage</p>
                    <p><strong>1000</strong> Clients</p>
                    <p><strong>FULL</strong> Support</p>
                  </div>
                </div>
                <div class="block-content block-content-full bg-body-light">
                  <span class="btn btn-primary px-4">Sign Up</span>
                </div>
              </template>
            </BaseBlock>
            <!-- END Business Plan -->
          </div>
          <div class="col-md-6 col-xl-3">
            <!-- VIP Plan -->
            <BaseBlock tag="a" href="javascript:void(0)" class="text-center" link-shadow>
              <template #content>
                <div class="block-header">
                  <h3 class="block-title">VIP</h3>
                </div>
                <div class="block-content bg-body-light">
                  <div class="py-2">
                    <p class="h1 fw-bold mb-2">$99</p>
                    <p class="h6 text-muted">per month</p>
                  </div>
                </div>
                <div class="block-content">
                  <div class="fs-sm py-2">
                    <p><strong>Unlimited</strong> Projects</p>
                    <p><strong>Unlimited</strong> Storage</p>
                    <p><strong>Unlimited</strong> Clients</p>
                    <p><strong>FULL</strong> Support</p>
                  </div>
                </div>
                <div class="block-content block-content-full bg-body-light">
                  <span class="btn btn-secondary px-4">Sign Up</span>
                </div>
              </template>
            </BaseBlock>
            <!-- END VIP Plan -->
          </div>
        </div>
      </div>
    </div>
    <!-- END Versions -->

    <!-- Power of Vite -->
    <div id="one-vue-power-of-vite" class="bg-body-extra-light">
      <div class="content content-full">
        <div class="py-5">
          <div class="row mb-5">
            <div class="col-md-6">
              <h2 class="h1 fw-bold mb-2">
                With the power of
                <span class="fw-normal">Vite</span>
              </h2>
              <p class="fs-lg fw-medium text-muted mb-0">
                We used the best toolkits and libraries, built by passionate people, to
                recreate OneUI from scratch and craft a Vue based version.
              </p>
            </div>
            <div
              class="col-md-6 d-none d-md-flex align-items-md-center justify-content-md-end"
            >
              <p class="h1 fw-bold text-body-bg-dark mb-0">Get Inspired.</p>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="d-flex align-items-center col-md-6 offset-lg-1 order-md-1">
            <div class="w-100 mb-5 mb-md-0 px-lg-6 position-relative">
              <div class="row">
                <div class="col-6 col-md-12">
                  <BaseBlock transparent class="bg-body-light">
                    <div class="py-4 py-md-6 text-center">
                      <svg
                        width="8rem"
                        height="8rem"
                        viewBox="0 0 410 404"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                      >
                        <path
                          d="M399.641 59.5246L215.643 388.545C211.844 395.338 202.084 395.378 198.228 388.618L10.5817 59.5563C6.38087 52.1896 12.6802 43.2665 21.0281 44.7586L205.223 77.6824C206.398 77.8924 207.601 77.8904 208.776 77.6763L389.119 44.8058C397.439 43.2894 403.768 52.1434 399.641 59.5246Z"
                          fill="url(#paint0_linear)"
                        />
                        <path
                          d="M292.965 1.5744L156.801 28.2552C154.563 28.6937 152.906 30.5903 152.771 32.8664L144.395 174.33C144.198 177.662 147.258 180.248 150.51 179.498L188.42 170.749C191.967 169.931 195.172 173.055 194.443 176.622L183.18 231.775C182.422 235.487 185.907 238.661 189.532 237.56L212.947 230.446C216.577 229.344 220.065 232.527 219.297 236.242L201.398 322.875C200.278 328.294 207.486 331.249 210.492 326.603L212.5 323.5L323.454 102.072C325.312 98.3645 322.108 94.137 318.036 94.9228L279.014 102.454C275.347 103.161 272.227 99.746 273.262 96.1583L298.731 7.86689C299.767 4.27314 296.636 0.855181 292.965 1.5744Z"
                          fill="url(#paint1_linear)"
                        />
                        <defs>
                          <linearGradient
                            id="paint0_linear"
                            x1="6.00017"
                            y1="32.9999"
                            x2="235"
                            y2="344"
                            gradientUnits="userSpaceOnUse"
                          >
                            <stop stop-color="#41D1FF" />
                            <stop offset="1" stop-color="#BD34FE" />
                          </linearGradient>
                          <linearGradient
                            id="paint1_linear"
                            x1="194.651"
                            y1="8.81818"
                            x2="236.076"
                            y2="292.989"
                            gradientUnits="userSpaceOnUse"
                          >
                            <stop stop-color="#FFEA83" />
                            <stop offset="0.0833333" stop-color="#FFDD35" />
                            <stop offset="1" stop-color="#FFA800" />
                          </linearGradient>
                        </defs>
                      </svg>
                    </div>
                  </BaseBlock>
                </div>
                <div class="col-6 col-md-12">
                  <BaseBlock transparent class="bg-body-light">
                    <div class="py-4 py-md-6 text-center">
                      <svg
                        height="8rem"
                        width="8rem"
                        viewBox="0 0 319 477"
                        xmlns="http://www.w3.org/2000/svg"
                      >
                        <linearGradient id="a">
                          <stop offset="0" stop-color="#52ce63" />
                          <stop offset="1" stop-color="#51a256" />
                        </linearGradient>
                        <linearGradient
                          id="b"
                          x1="55.342%"
                          x2="42.817%"
                          xlink:href="#a"
                          y1="0%"
                          y2="42.863%"
                        />
                        <linearGradient
                          id="c"
                          x1="55.349%"
                          x2="42.808%"
                          xlink:href="#a"
                          y1="0%"
                          y2="42.863%"
                        />
                        <linearGradient id="d" x1="50%" x2="50%" y1="0%" y2="58.811%">
                          <stop offset="0" stop-color="#8ae99c" />
                          <stop offset="1" stop-color="#52ce63" />
                        </linearGradient>
                        <linearGradient
                          id="e"
                          x1="51.378%"
                          x2="44.585%"
                          y1="17.473%"
                          y2="100%"
                        >
                          <stop offset="0" stop-color="#ffe56c" />
                          <stop offset="1" stop-color="#ffc63a" />
                        </linearGradient>
                        <g fill="none" fill-rule="evenodd" transform="translate(-34 -24)">
                          <path
                            d="M103.95 258.274c44.362-4.36 60.015-40.391 65.354-94.7s-30.933-103.45-46.02-101.967c-15.089 1.483-63.04 58.905-68.378 113.213-5.338 54.308 4.683 87.815 49.045 83.454z"
                            fill="url(#b)"
                            transform="rotate(-38 137.962 147.099)"
                          />
                          <path
                            d="M275.877 258.274c44.361 4.36 53.167-29.265 47.828-83.573-5.338-54.309-52.073-111.611-67.161-113.094-15.088-1.483-52.575 47.54-47.236 101.848s22.207 90.458 66.569 94.819z"
                            fill="url(#c)"
                            transform="rotate(52 240.026 189.003)"
                          />
                          <path
                            d="M188.37 216.876c39.942 0 50.953-38.252 50.953-97.898C239.323 59.33 201.955.876 188.37.876s-52.047 58.455-52.047 118.102c0 59.646 12.105 97.898 52.047 97.898z"
                            fill="url(#d)"
                            transform="rotate(7 8.977 277.799)"
                          />
                          <path
                            d="M184.473 501C267.593 501 335 476.855 335 367.355S267.592 168 184.473 168C101.355 168 34 257.855 34 367.355S101.355 501 184.473 501z"
                            fill="url(#e)"
                          />
                          <ellipse cx="260.5" cy="335" fill="#eaadcc" rx="21.5" ry="10" />
                          <ellipse
                            cx="102.5"
                            cy="329"
                            fill="#eaadcc"
                            rx="21.5"
                            ry="10"
                            transform="rotate(7 102.5 329)"
                          />
                          <g>
                            <path
                              d="M198.248 331.459c-6.471 5.259-13.945 7.404-22.422 6.435-8.478-.969-14.761-4.487-18.85-10.556"
                              stroke="#000"
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="6"
                            />
                            <path
                              d="M114.983 279.418a21.435 21.435 0 0 1 15.414 5.762 21.431 21.431 0 0 1 6.824 14.974 21.433 21.433 0 0 1-5.763 15.414 21.434 21.434 0 0 1-14.975 6.824 21.43 21.43 0 0 1-15.413-5.763 21.434 21.434 0 0 1-6.823-14.975 21.432 21.432 0 0 1 5.762-15.413 21.431 21.431 0 0 1 14.974-6.823z"
                              fill="#000"
                            />
                            <path
                              d="M116.112 297.39a7.001 7.001 0 0 0-13.992.488 7 7 0 0 0 13.992-.489z"
                              fill="#fff"
                            />
                            <path
                              d="M245.253 284.875a21.433 21.433 0 0 1 15.414 5.762 21.432 21.432 0 0 1 6.824 14.974 21.433 21.433 0 0 1-5.763 15.414 21.432 21.432 0 0 1-14.974 6.824 21.433 21.433 0 0 1-15.413-5.763 21.433 21.433 0 0 1-6.824-14.975 21.432 21.432 0 0 1 5.763-15.412 21.433 21.433 0 0 1 14.973-6.824z"
                              fill="#000"
                            />
                            <g fill="#fff">
                              <path
                                d="M134.223 300.259c.356 10.212-7.633 18.778-17.845 19.134-10.21.357-18.776-7.63-19.133-17.843-.356-10.211 7.631-18.777 17.842-19.134 10.212-.357 18.78 7.631 19.136 17.843zm5.996-.21c-.472-13.523-11.818-24.102-25.341-23.63-13.523.473-24.101 11.817-23.63 25.34.473 13.524 11.817 24.103 25.34 23.63 13.524-.471 24.103-11.816 23.631-25.34zM264.492 305.715c.357 10.213-7.63 18.779-17.843 19.135-10.21.357-18.777-7.63-19.134-17.843-.357-10.211 7.632-18.777 17.843-19.134 10.212-.357 18.778 7.631 19.134 17.842zm5.997-.209c-.472-13.523-11.817-24.102-25.34-23.63-13.523.473-24.103 11.817-23.63 25.34.472 13.524 11.817 24.103 25.34 23.63 13.524-.471 24.102-11.816 23.63-25.34z"
                                fill-rule="nonzero"
                                stroke="#fff"
                                stroke-width="3"
                              />
                              <path
                                d="M246.381 302.846a7 7 0 1 0-13.992.49 7 7 0 0 0 13.992-.49z"
                              />
                            </g>
                          </g>
                          <g stroke-linecap="round" stroke-width="11">
                            <g stroke="#ecb732">
                              <path d="m70.5 377.5 74 77M134.5 386.5l-47 50" />
                            </g>
                            <g stroke="#ecb732">
                              <path d="m297.5 377.5-74 77M233.5 386.5l47 50" />
                            </g>
                            <g stroke="#ffc73b">
                              <path d="m214.5 207.5-49 49M204.5 256.5l-49-49" />
                            </g>
                          </g>
                        </g>
                      </svg>
                    </div>
                  </BaseBlock>
                </div>
              </div>
            </div>
          </div>
          <div class="d-md-flex align-items-md-center col-md-6 col-lg-5 order-md-0">
            <div>
              <div class="d-flex push">
                <div class="item item-rounded bg-body flex-shrink-0">
                  <i class="fa fa-2x fa-rocket"></i>
                </div>
                <div class="ms-4">
                  <h4 class="mb-2">Vite</h4>
                  <p class="text-muted">
                    Next generation frontend tooling. A build tool that aims to provide a
                    faster and leaner development experience for modern web projects.
                  </p>
                </div>
              </div>
              <div class="d-flex push">
                <div class="item item-rounded bg-body flex-shrink-0">
                  <i class="fab fa-2x fa-vuejs"></i>
                </div>
                <div class="ms-4">
                  <h4 class="mb-2">Vue.js 3</h4>
                  <p class="text-muted">
                    The latest version of the popular open source JavaScript framework. An
                    approachable, performant and versatile one for building web user
                    interfaces.
                  </p>
                </div>
              </div>
              <div class="d-flex push">
                <div class="item item-rounded bg-body flex-shrink-0">
                  <i class="fa fa-2x fa-database"></i>
                </div>
                <div class="ms-4">
                  <h4 class="mb-2">Pinia</h4>
                  <p class="text-muted">
                    The latest store library for Vue.js 3 based projects which allows you
                    to share a state across components/pages.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- END Power of Vite -->

    <!-- Trusted By -->
    <div id="one-vue-trusted-by" class="bg-body-light">
      <div class="content py-6">
        <div class="py-5 text-center">
          <div class="row justify-content-center">
            <div class="col-md-8">
              <p>
                <i class="fa fa-5x fa-award text-warning"></i>
              </p>
              <h2 class="h1 fw-bold mb-2">Trusted by over 4,900 developers and teams</h2>
              <p class="fs-lg fw-medium text-muted mb-0">
                <a href="https://1.envato.market/AVD6j">OneUI</a> is one of the Best
                Seller and Best Rated admin templates on Themeforest. Its success made
                possible the development of Vue Edition.
              </p>
            </div>
          </div>
        </div>
        <div class="row items-push text-center py-4">
          <div class="col-sm-4">
            <div class="h1 fw-bold mb-1">4,900+</div>
            <div class="fw-semibold text-muted">Purchases</div>
          </div>
          <div class="col-sm-4">
            <div class="h1 fw-bold mb-1">280+</div>
            <div class="fw-semibold text-muted">5 Star Ratings</div>
          </div>
          <div class="col-sm-4">
            <div class="h1 fw-bold mb-1">31+</div>
            <div class="fw-semibold text-muted">Free Updates</div>
          </div>
        </div>
      </div>
    </div>
    <!-- END Trusted By -->

    <!-- Features -->
    <div id="one-vue-features" class="bg-body-extra-light">
      <div class="content content-full">
        <div class="py-5">
          <div class="row mb-5">
            <div class="col-md-6">
              <h2 class="h1 fw-bold mb-2">
                Sophisticated
                <span class="fw-normal">Features</span>
              </h2>
              <p class="fs-lg fw-medium text-muted mb-0">
                Comes packed with great features and development tools, based on OneUI 5
                design.
              </p>
            </div>
            <div
              class="col-md-6 d-none d-md-flex align-items-md-center justify-content-md-end"
            >
              <p class="h1 fw-bold text-body-bg-dark mb-0">Carefully Crafted.</p>
            </div>
          </div>
          <div class="row items-push">
            <div class="col-sm-6 col-md-4 col-xl-3">
              <!-- Bootstrap 5 -->
              <div class="item item-rounded bg-amethyst-lighter my-4">
                <i class="fa fa-2x fa-fire text-amethyst"></i>
              </div>
              <h4 class="mb-2">Bootstrap 5</h4>
              <p class="text-muted">
                The latest Bootstrap version now powers OneUI Vue. Amazing new features
                and utilities are ready for you to use.
              </p>
              <!-- END Bootstrap 5 -->
            </div>
            <div class="col-sm-6 col-md-4 col-xl-3">
              <!-- Sass -->
              <div class="item item-rounded bg-smooth-lighter my-4">
                <i class="fab fa-2x fa-sass text-smooth"></i>
              </div>
              <h4 class="mb-2">Sass</h4>
              <p class="text-muted">
                OneUI Vue was built with Sass, overriding and extending Bootstrap in an
                intelligent way to ensure a perfect and modular workflow.
              </p>
              <!-- END Sass -->
            </div>
            <div class="col-sm-6 col-md-4 col-xl-3">
              <!-- ES6 -->
              <div class="item item-rounded bg-flat-lighter my-4">
                <span class="fw-bold text-flat">ES6</span>
              </div>
              <h4 class="mb-2">ECMAScript 6</h4>
              <p class="text-muted">
                ES6, the new major JavaScript release, is used, which enables us writing
                cleaner and better code.
              </p>
              <!-- END ES6 -->
            </div>
            <div class="col-sm-6 col-md-4 col-xl-3">
              <!-- Font Awesome 6 -->
              <div class="item item-rounded bg-warning-light my-4">
                <i class="fab fa-2x fa-font-awesome text-warning"></i>
              </div>
              <h4 class="mb-2">Font Awesome 6</h4>
              <p class="text-muted">
                OneUI Vue comes packed with one of the most popular icon sets, bringing
                you over 2000 freshly made icons for your projects.
              </p>
              <!-- END Font Awesome 6 -->
            </div>
            <div class="col-sm-6 col-md-4 col-xl-3">
              <!-- Composition API -->
              <div class="item item-rounded bg-city-lighter my-4">
                <i class="fa fa-2x fa-code text-city"></i>
              </div>
              <h4 class="mb-2">Composition API</h4>
              <p class="text-muted">
                A set of APIs that allows us to author Vue components using imported
                functions instead of declaring options. We are also using the
                <code>&lt;script setup&gt;</code> syntactic sugar.
              </p>
              <!-- END Composition API -->
            </div>
            <div class="col-sm-6 col-md-4 col-xl-3">
              <!-- Prettier -->
              <div class="item item-rounded bg-default-lighter my-4">
                <i class="fa fa-2x fa-file-lines text-default"></i>
              </div>
              <h4 class="mb-2">Prettier</h4>
              <p class="text-muted">
                All included files are formatted using the popular opinionated code
                formatter for the best readability and coding pleasure.
              </p>
              <!-- END Prettier -->
            </div>
            <div class="col-sm-6 col-md-4 col-xl-3">
              <!-- Lightning Fast HMR -->
              <div class="item item-rounded bg-modern-lighter my-4">
                <i class="fa fa-2x fa-bolt-lightning text-modern"></i>
              </div>
              <h4 class="mb-2">Lightning Fast HMR</h4>
              <p class="text-muted">
                Hot Module Replacement (HMR) that stays fast regardless of app size,
                providing a faster development environment.
              </p>
              <!-- END Lightning Fast HMR -->
            </div>
            <div class="col-sm-6 col-md-4 col-xl-3">
              <!-- Autoprefixer -->
              <div class="item item-rounded bg-smooth-lighter my-4">
                <i class="fab fa-2x fa-autoprefixer text-smooth"></i>
              </div>
              <h4 class="mb-2">Autoprefixer</h4>
              <p class="text-muted">
                Peace of mind when working with Sass. Use the latest CSS syntax and
                Autoprefixer will auto add any required prefixes for older browsers.
              </p>
              <!-- END Autoprefixer -->
            </div>
            <div class="col-sm-6 col-md-4 col-xl-3">
              <!-- Components -->
              <div class="item item-rounded bg-success-light my-4">
                <i class="fa fa-2x fa-truck-loading text-success"></i>
              </div>
              <h4 class="mb-2">Components</h4>
              <p class="text-muted">
                Custom vital components are available to be used in your Vue.js templates,
                making it easier to structure your page content or add interactivity.
              </p>
              <!-- END Components -->
            </div>
            <div class="col-sm-6 col-md-4 col-xl-3">
              <!-- Improved Design -->
              <div class="item item-rounded bg-info-light my-4">
                <i class="fa fa-2x fa-brush text-info"></i>
              </div>
              <h4 class="mb-2">Improved Design</h4>
              <p class="text-muted">
                Small touches and improvements were introduced throughout the template.
                From colors to layout and from custom elements to plugins.
              </p>
              <!-- END Improved Design -->
            </div>
            <div class="col-sm-6 col-md-4 col-xl-3">
              <!-- Dark Mode -->
              <div class="item item-rounded bg-body my-4">
                <i class="fa fa-2x fa-moon text-dark"></i>
              </div>
              <h4 class="mb-2">Dark Mode</h4>
              <p class="text-muted">
                It is finally here and looks amazing! It was made to work with all color
                themes and included pages. Choose between light, dark or system preference
                out of the box.
              </p>
              <!-- END Dark Mode -->
            </div>
            <div class="col-sm-6 col-md-4 col-xl-3">
              <!-- APIs -->
              <div class="item item-rounded bg-warning-light my-4">
                <i class="fa fa-2x fa-star text-warning"></i>
              </div>
              <h4 class="mb-2">APIs</h4>
              <p class="text-muted">
                Easily manipulate blocks and layout features on the fly from any view of
                your application. Either with buttons or JS code.
              </p>
              <!-- END APIs -->
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- END Features -->

    <!-- Reviews -->
    <div id="one-vue-reviews" class="bg-body-light">
      <div class="content content-full">
        <div class="py-5">
          <div class="row mb-5">
            <div class="col-md-6">
              <h2 class="h1 fw-bold mb-2">
                Real Customer
                <span class="fw-normal">Reviews</span>
              </h2>
              <p class="fs-lg fw-medium text-muted mb-0">
                Check out what web developers and people in tech have written about the
                main OneUI framework.
              </p>
            </div>
            <div
              class="col-md-6 d-none d-md-flex align-items-md-center justify-content-md-end"
            >
              <p class="h1 fw-bold text-body-bg-dark mb-0">Truly Loved.</p>
            </div>
          </div>
          <div class="row items-push">
            <div class="col-md-4">
              <div
                class="d-inline-block px-2 py-1 rounded-3 bg-primary-lighter text-primary mb-2"
              >
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
              </div>
              <p class="text-muted mb-2">
                A combination of flexibility and ease of use. The design is beautiful, but
                I really value the ease in which I was able to integrate this into my
                development workflow and platform.
              </p>
              <p class="fs-sm fw-medium">
                For Other by
                <span class="fw-semibold">appeality</span>
              </p>
            </div>
            <div class="col-md-4">
              <div
                class="d-inline-block px-2 py-1 rounded-3 bg-primary-lighter text-primary mb-2"
              >
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
              </div>
              <p class="text-muted mb-2">
                While reading the docs i can feel that you literally gave your heart to
                create this project. It is a high quality piece of work, thanks for
                sharing it!
              </p>
              <p class="fs-sm fw-medium">
                For Code Quality by
                <span class="fw-semibold">msagi</span>
              </p>
            </div>
            <div class="col-md-4">
              <div
                class="d-inline-block px-2 py-1 rounded-3 bg-primary-lighter text-primary mb-2"
              >
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
              </div>
              <p class="text-muted mb-2">
                This is my first purchase on Themeforest and I am delighted. Everything
                from the design to the code is beautifully crafted and the customer
                support is great also. Congratulations pixelcave.
              </p>
              <p class="fs-sm fw-medium">
                For Customizability by
                <span class="fw-semibold">CaravelaThemes</span>
              </p>
            </div>
          </div>
          <div class="row items-push">
            <div class="col-md-4">
              <div
                class="d-inline-block px-2 py-1 rounded-3 bg-primary-lighter text-primary mb-2"
              >
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
              </div>
              <p class="text-muted mb-2">
                One of the most well thought-through and comprehensive Themeforest
                templates available. Consistently excellent design and broad feature base.
                Highly Recommended!
              </p>
              <p class="fs-sm fw-medium">
                For Feature Availability by
                <span class="fw-semibold">stephenhird</span>
              </p>
            </div>
            <div class="col-md-4">
              <div
                class="d-inline-block px-2 py-1 rounded-3 bg-primary-lighter text-primary mb-2"
              >
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
              </div>
              <p class="text-muted mb-2">
                One of the best paid for downloads I have ever made. Has so many features
                which have all been designed and put together absolutely brilliantly.
              </p>
              <p class="fs-sm fw-medium">
                For Design Quality by
                <span class="fw-semibold">weblid</span>
              </p>
            </div>
            <div class="col-md-4">
              <div
                class="d-inline-block px-2 py-1 rounded-3 bg-primary-lighter text-primary mb-2"
              >
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
              </div>
              <p class="text-muted mb-2">
                This is hands down the best template I have ever come across. It has
                absolutely everything you need right there laid out and easy to find. I
                couldn't recommend this template enough!
              </p>
              <p class="fs-sm fw-medium">
                For Feature Availability by
                <span class="fw-semibold">dhowa021</span>
              </p>
            </div>
          </div>
          <div class="row items-push">
            <div class="col-md-4">
              <div
                class="d-inline-block px-2 py-1 rounded-3 bg-primary-lighter text-primary mb-2"
              >
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
              </div>
              <p class="text-muted mb-2">
                After using this Admin template for 6 months...we are still delighted.
                This template has everything. It has obviously been designed with much
                care and detail. Very intuitive, Easy to use. And we're still finding
                functionality that we hadn't discovered before. Well done to the developer
                and thanks for putting your heart-and-soul into this template.
              </p>
              <p class="fs-sm fw-medium">
                For Other by
                <span class="fw-semibold">conorhannah</span>
              </p>
            </div>
            <div class="col-md-4">
              <div
                class="d-inline-block px-2 py-1 rounded-3 bg-primary-lighter text-primary mb-2"
              >
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
              </div>
              <p class="text-muted mb-2">
                This is the best UI I have ever came across, this UI theme is absolutely
                perfect in Every Way :) Really happy with the purchase.
              </p>
              <p class="fs-sm fw-medium">
                For Other by
                <span class="fw-semibold">spmtumblr</span>
              </p>
            </div>
            <div class="col-md-4">
              <div
                class="d-inline-block px-2 py-1 rounded-3 bg-primary-lighter text-primary mb-2"
              >
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
              </div>
              <p class="text-muted mb-2">
                I have spent two days researching admin themes. There are a couple of
                really good ones out there, but this one came out at the very top for me.
                Looks great, on both desktop and mobile, the feature set is amazing, the
                documentation looks very good. I haven't started implementing yet, but
                this deserves five stars already.
              </p>
              <p class="fs-sm fw-medium">
                For Design Quality by
                <span class="fw-semibold">dvartok</span>
              </p>
            </div>
          </div>
          <div class="row items-push">
            <div class="col-md-4">
              <div
                class="d-inline-block px-2 py-1 rounded-3 bg-primary-lighter text-primary mb-2"
              >
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
              </div>
              <p class="text-muted mb-2">
                This is one of the best all-around packages I've purchased from
                ThemeForest. Not only is the Documentation is excellent and well-written,
                but the code itself is intelligently built and a pleasure to work with.
                Thanks for doing such great work.
              </p>
              <p class="fs-sm fw-medium">
                For Other by
                <span class="fw-semibold">rshaffaf</span>
              </p>
            </div>
            <div class="col-md-4">
              <div
                class="d-inline-block px-2 py-1 rounded-3 bg-primary-lighter text-primary mb-2"
              >
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
              </div>
              <p class="text-muted mb-2">
                The best admin template ever, no doubt of it!!
              </p>
              <p class="fs-sm fw-medium">
                For Other by
                <span class="fw-semibold">kaladrian</span>
              </p>
            </div>
            <div class="col-md-4">
              <div
                class="d-inline-block px-2 py-1 rounded-3 bg-primary-lighter text-primary mb-2"
              >
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
              </div>
              <p class="text-muted mb-2">Easily the best admin template you can find.</p>
              <p class="fs-sm fw-medium">
                For Code Quality by
                <span class="fw-semibold">nozebra_dk</span>
              </p>
            </div>
          </div>
          <div class="row items-push">
            <div class="col-md-4">
              <div
                class="d-inline-block px-2 py-1 rounded-3 bg-primary-lighter text-primary mb-2"
              >
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
              </div>
              <p class="text-muted mb-2">
                Everything's perfect! Good design! Best performance I've ever use! And the
                best thing, fastest support I've seen! 5 star satisfaction!
              </p>
              <p class="fs-sm fw-medium">
                For Customer Support by
                <span class="fw-semibold">arkheacol04</span>
              </p>
            </div>
            <div class="col-md-4">
              <div
                class="d-inline-block px-2 py-1 rounded-3 bg-primary-lighter text-primary mb-2"
              >
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
              </div>
              <p class="text-muted mb-2">
                This is an amazing, multi purpose, and very well designed and structured
                template. I rarely write a review but this template deserves the support.
                It is distinguished.
              </p>
              <p class="fs-sm fw-medium">
                For Design Quality by
                <span class="fw-semibold">maa83</span>
              </p>
            </div>
            <div class="col-md-4">
              <div
                class="d-inline-block px-2 py-1 rounded-3 bg-primary-lighter text-primary mb-2"
              >
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
              </div>
              <p class="text-muted mb-2">
                Long story short: I really enjoy using the templates made by pixelcave.
                The code is very flexible and well structured, the documentation is very
                good - everything you need.
              </p>
              <p class="fs-sm fw-medium">
                For Code Quality by
                <span class="fw-semibold">Master_rg</span>
              </p>
            </div>
          </div>
          <div class="row items-push">
            <div class="col-md-4">
              <div
                class="d-inline-block px-2 py-1 rounded-3 bg-primary-lighter text-primary mb-2"
              >
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
              </div>
              <p class="text-muted mb-2">
                It's awesome, not only the design is marvelous, the code and documentation
                helps easy customization.
              </p>
              <p class="fs-sm fw-medium">
                For Design Quality by
                <span class="fw-semibold">alperaydyn2</span>
              </p>
            </div>
            <div class="col-md-4">
              <div
                class="d-inline-block px-2 py-1 rounded-3 bg-primary-lighter text-primary mb-2"
              >
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
              </div>
              <p class="text-muted mb-2">Awesome !!! Thanks for a so great template !!</p>
              <p class="fs-sm fw-medium">
                For Feature Availability by
                <span class="fw-semibold">Markuitos</span>
              </p>
            </div>
            <div class="col-md-4">
              <div
                class="d-inline-block px-2 py-1 rounded-3 bg-primary-lighter text-primary mb-2"
              >
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
                <i class="fa fa-fw fa-star"></i>
              </div>
              <p class="text-muted mb-2">
                Awesome code, works really well, well documented!
              </p>
              <p class="fs-sm fw-medium">
                For Flexibility by
                <span class="fw-semibold">corverdevelopment</span>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- END Reviews -->

    <!-- Call To Action -->
    <div id="one-vue-call-to-action" class="bg-body-extra-light">
      <div class="content content-full">
        <div class="py-5 py-md-8 text-center">
          <h2 class="fw-bold mb-2">
            Crafted with
            <i class="fa fa-fw fa-heart text-city"></i> by
            <a class="link-fx" href="https://1.envato.market/ydb">pixelcave</a>
          </h2>
          <p class="fs-lg fw-medium text-muted mb-4">
            Passionate web design and development with over 15,000 customers worldwide.
          </p>
          <a
            class="btn btn-success py-2 px-3 m-1"
            href="https://1.envato.market/5Noyb"
            v-click-ripple
          >
            <i class="fa fa-fw fa-shopping-cart opacity-50"></i>
            <span class="ms-2">Purchase</span>
          </a>
          <RouterLink
            :to="{ name: 'dashboard' }"
            class="btn btn-primary py-2 px-3 m-1"
            v-click-ripple
          >
            <i class="fa fa-fw fa-desktop opacity-50 me-1"></i> Live preview
          </RouterLink>
        </div>
      </div>
    </div>
    <!-- END Call To Action -->

    <!-- Footer -->
    <footer id="page-footer" class="bg-body-light">
      <div class="content py-5">
        <div class="row fs-sm fw-medium">
          <div class="col-sm-6 order-sm-2 py-1 text-center text-sm-end">
            Crafted with
            <i class="fa fa-heart text-danger"></i> by
            <a class="fw-semibold" href="https://1.envato.market/ydb">pixelcave</a>
          </div>
          <div class="col-sm-6 order-sm-1 py-1 text-center text-sm-start">
            <a class="fw-semibold" href="https://1.envato.market/5Noyb">{{
              store.app.name + " " + store.app.version
            }}</a>
            &copy; {{ store.app.copyright }}
          </div>
        </div>
      </div>
    </footer>
    <!-- END Footer -->
  </div>
</template>

<style scoped>
.central-landing-hero {
  position: relative;
  min-height: 100vh;
  display: flex;
  align-items: center;
  background-image: url("/assets/fonts/image/landing-back.png");
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  padding-top: 0;
  margin-top: 0;
}

.central-landing-hero__overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    90deg,
    rgba(15, 23, 42, 0.88) 0%,
    rgba(30, 41, 59, 0.15) 45%,
    rgba(30, 41, 59, 0.15) 55%,
    rgba(15, 23, 42, 0.88) 100%
  );
  pointer-events: none;
}

.central-landing-hero__content {
  position: relative;
  z-index: 1;
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.central-landing-hero__container {
  width: 100%;
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 2rem;
}

.central-landing-hero__text {
  color: #fff;
}

.hero-text-offset {
  transform: translateX(0);
}

.central-landing-hero__badge {
  display: inline-flex;
  align-items: center;
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
  font-weight: 600;
  color: rgba(255, 255, 255, 0.95);
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.3);
  border-radius: 9999px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.central-landing-hero__title {
  font-size: clamp(1.75rem, 4vw, 2.75rem);
  font-weight: 800;
  color: #fff;
  letter-spacing: -0.025em;
  line-height: 1.25;
  text-shadow: 0 4px 24px rgba(0, 0, 0, 0.3);
  max-width: 100%;
}

.central-landing-hero__subtitle {
  font-size: clamp(1rem, 1.5vw, 1.15rem);
  color: rgba(255, 255, 255, 0.9);
  line-height: 1.65;
  max-width: 100%;
  margin: 0;
  text-shadow: 0 2px 12px rgba(0, 0, 0, 0.2);
}

/* Compact form card on the side */
.central-landing-form-card {
  background: rgba(255, 255, 255, 0.06);
  backdrop-filter: blur(20px);
  border-radius: 1.5rem;
  box-shadow: 0 24px 60px rgba(0, 0, 0, 0.35), 0 0 0 1px rgba(255, 255, 255, 0.25);
  overflow: hidden;
  max-width: 100%;
  margin: 0 auto;
  margin-top: 80px;
}

.central-landing-form-card__header {
  padding: 1.25rem 1.75rem 1rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.15);
  background: rgba(255, 255, 255, 0.08);
}

.central-landing-form-card__title {
  font-size: 1.25rem;
  font-weight: 700;
  color: #fff;
  margin: 0 0 0.25rem 0;
  text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.central-landing-form-card__subtitle {
  font-size: 0.875rem;
  color: rgba(255, 255, 255, 0.85);
  margin: 0;
  text-shadow: 0 1px 4px rgba(0, 0, 0, 0.15);
}

.central-landing-form-card__body {
  padding: 1.25rem 1.75rem 1.5rem;
}

.central-landing-form__field {
  margin-bottom: 0.85rem;
}

.central-landing-form__field .form-control,
.central-landing-form__field .form-select {
  border-radius: 0.5rem;
  border: 1px solid rgba(255, 255, 255, 0.25);
  background: rgba(255, 255, 255, 0.95);
  font-size: 0.95rem;
  padding: 1.5rem 0.875rem 0.5rem;
  min-height: 3.5rem;
  color: #1e293b;
}

.central-landing-form__field label {
  font-size: 0.75rem;
  padding: 0.875rem 0.875rem;
  color: rgba(30, 41, 59, 0.7);
  line-height: 1.2;
}

.central-landing-form__field .form-floating > label {
  transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem);
}

.central-landing-form__field .form-floating > .form-control:focus ~ label,
.central-landing-form__field .form-floating > .form-control:not(:placeholder-shown) ~ label,
.central-landing-form__field .form-floating > .form-select ~ label {
  opacity: 0.75;
  transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem);
}

.central-landing-form__field .form-control:focus,
.central-landing-form__field .form-select:focus {
  border-color: #0ea5e9;
  background: #fff;
  box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.25);
}

.central-landing-form__field .form-select {
  background-position: right 0.75rem center;
  background-size: 16px 12px;
}

.central-landing-form__row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0 0.85rem;
}

.central-landing-form__submit {
  width: 100%;
  margin-top: 0.75rem;
  padding: 0.7rem 1.25rem;
  font-weight: 600;
  font-size: 1rem;
  background: #0ea5e9;
  border: none;
  border-radius: 0.5rem;
  color: #fff;
  box-shadow: 0 4px 12px rgba(14, 165, 233, 0.35);
  transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
}

.central-landing-form__submit:hover:not(:disabled) {
  background: #0284c7;
  color: #fff;
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(14, 165, 233, 0.45);
}

.central-landing-form__submit:disabled {
  opacity: 0.65;
  cursor: not-allowed;
}

@media (min-width: 1400px) {
  .central-landing-hero__container {
    max-width: 1600px;
    padding: 0 3rem;
  }
  .hero-text-offset {
    transform: translateX(450px) !important;
    max-width: 420px !important;
  }
  .central-landing-hero__title {
    max-width: 420px !important;
    font-size: 2.5rem !important;
    line-height: 1.2 !important;
  }
  .central-landing-hero__subtitle {
    max-width: 420px !important;
    font-size: 1.1rem !important;
    line-height: 1.5 !important;
  }
}

@media (min-width: 1200px) and (max-width: 1399.98px) {
  .central-landing-hero__container {
    max-width: 1400px;
    padding: 0 2.5rem;
  }
  .hero-text-offset {
    transform: translateX(390px) !important;
    max-width: 380px !important;
  }
  .central-landing-hero__title {
    max-width: 380px !important;
    font-size: 2.2rem !important;
    line-height: 1.2 !important;
  }
  .central-landing-hero__subtitle {
    max-width: 380px !important;
    font-size: 1rem !important;
    line-height: 1.5 !important;
  }
}

@media (min-width: 992px) and (max-width: 1199.98px) {
  .hero-text-offset {
    transform: translateX(320px) !important;
    max-width: 340px !important;
  }
  .central-landing-hero__title {
    max-width: 340px !important;
    font-size: 2rem !important;
    line-height: 1.2 !important;
  }
  .central-landing-hero__subtitle {
    max-width: 340px !important;
    font-size: 0.95rem !important;
    line-height: 1.5 !important;
  }
}

@media (max-width: 991.98px) {
  .hero-text-offset {
    margin-left: 0;
  }
  .central-landing-form-card {
    max-width: 540px;
  }
  .central-landing-hero__container {
    padding: 0 1rem;
  }
  .central-landing-hero__title {
    font-size: clamp(1.5rem, 5vw, 2.25rem);
    max-width: 100%;
    margin-left: auto;
    margin-right: auto;
  }
  .central-landing-hero__subtitle {
    font-size: 1rem;
    max-width: 100%;
    margin-left: auto;
    margin-right: auto;
  }
  .central-landing-hero__text {
    text-align: center;
  }
}

@media (max-width: 575.98px) {
  .central-landing-form__row {
    grid-template-columns: 1fr;
    gap: 0;
  }
  .central-landing-form-card__body {
    padding: 1rem 1.25rem 1.25rem;
  }
  .central-landing-hero__container {
    padding: 0 0.75rem;
  }
}
</style>

<style>
/* Add side margins to all sections for consistent layout - override content-full */
#one-vue-hero .content-full,
#one-vue-hero-after .content-full,
#one-vue-versions .content-full,
#one-vue-power-of-vite .content-full,
#one-vue-trusted-by .content-full,
#one-vue-features .content-full,
#one-vue-reviews .content-full,
#one-vue-call-to-action .content-full,
#page-footer .content {
  max-width: 1320px !important;
  margin-left: auto !important;
  margin-right: auto !important;
  padding-left: 1.5rem !important;
  padding-right: 1.5rem !important;
}

@media (min-width: 1400px) {
  #one-vue-hero .content-full,
  #one-vue-hero-after .content-full,
  #one-vue-versions .content-full,
  #one-vue-power-of-vite .content-full,
  #one-vue-trusted-by .content-full,
  #one-vue-features .content-full,
  #one-vue-reviews .content-full,
  #one-vue-call-to-action .content-full,
  #page-footer .content {
    max-width: 1400px !important;
  }
}

@media (max-width: 991.98px) {
  #one-vue-hero .content-full,
  #one-vue-hero-after .content-full,
  #one-vue-versions .content-full,
  #one-vue-power-of-vite .content-full,
  #one-vue-trusted-by .content-full,
  #one-vue-features .content-full,
  #one-vue-reviews .content-full,
  #one-vue-call-to-action .content-full,
  #page-footer .content {
    padding-left: 1rem !important;
    padding-right: 1rem !important;
  }
}

@media (max-width: 575.98px) {
  #one-vue-hero .content-full,
  #one-vue-hero-after .content-full,
  #one-vue-versions .content-full,
  #one-vue-power-of-vite .content-full,
  #one-vue-trusted-by .content-full,
  #one-vue-features .content-full,
  #one-vue-reviews .content-full,
  #one-vue-call-to-action .content-full,
  #page-footer .content {
    padding-left: 0.75rem !important;
    padding-right: 0.75rem !important;
  }
}

/* Global adjustments for demo sections below - make them more compact like original */
#one-vue-hero .fs-2 {
  font-size: 1.75rem !important;
}

#one-vue-hero .fs-5 {
  font-size: 1.05rem !important;
}

#one-vue-hero .pt-7 {
  padding-top: 3rem !important;
}

#one-vue-hero .pb-9 {
  padding-bottom: 4rem !important;
}

#one-vue-versions .h1,
#one-vue-power-of-vite .h1,
#one-vue-features .h1,
#one-vue-trusted-by .h1,
#one-vue-reviews .h1,
#one-vue-call-to-action .h1 {
  font-size: 1.75rem !important;
}

#one-vue-versions .py-5,
#one-vue-power-of-vite .py-5,
#one-vue-features .py-5 {
  padding-top: 2.5rem !important;
  padding-bottom: 2.5rem !important;
}

#one-vue-versions h4,
#one-vue-power-of-vite h4,
#one-vue-features h4 {
  font-size: 1rem !important;
  margin-bottom: 0.75rem !important;
}

#one-vue-versions .fs-lg,
#one-vue-power-of-vite .fs-lg,
#one-vue-features .fs-lg {
  font-size: 1rem !important;
}

#one-vue-trusted-by .py-6 {
  padding-top: 2.5rem !important;
  padding-bottom: 2.5rem !important;
}

#one-vue-trusted-by .py-5 {
  padding-top: 2rem !important;
  padding-bottom: 2rem !important;
}

#one-vue-reviews .items-push > div {
  margin-bottom: 2rem !important;
}

#one-vue-call-to-action .py-5 {
  padding-top: 2.5rem !important;
  padding-bottom: 2.5rem !important;
}

#one-vue-call-to-action .py-md-8 {
  padding-top: 3rem !important;
  padding-bottom: 3rem !important;
}

#page-footer .py-5 {
  padding-top: 2rem !important;
  padding-bottom: 2rem !important;
}

/* Card sizes in versions section */
.block-content {
  padding: 1rem !important;
}

.item.item-rounded {
  width: 3rem !important;
  height: 3rem !important;
  font-size: 1.25rem !important;
}
</style>
