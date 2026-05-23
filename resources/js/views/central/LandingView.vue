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

const isSubmitting = ref(false);

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

function getPayload() {
  const fullDomain = domain.value || `${form.domain || ""}.${import.meta.env.VITE_APP_NAME || "escuelapresente.com"}`;
  return {
    ...form,
    domain: fullDomain,
  };
}

function onSubmit() {
  if (isSubmitting.value) return;

  const payload = getPayload();
  if (!payload.domain || !payload.school_name || !payload.cct || !payload.email || !payload.password || !payload.phone) {
    Toast.fire({
      icon: "warning",
      title: "Complete todos los campos requeridos.",
    });
    return;
  }

  isSubmitting.value = true;
  api
    .post(`/tenants/public`, payload)
    .then((res) => {
      const ok = res.data && res.data.success !== false;
      if (ok) {
        Swal.fire(
          "¡Excelente!",
          "Su cuenta ha sido creada. Revisa tu correo para acceder al sistema.",
          "success"
        );
        Object.assign(form, initialForm);
        domain.value = "";
      } else {
        Toast.fire({
          icon: "error",
          title: res.data?.message || "Ocurrió un error",
        });
      }
    })
    .catch((err) => {
      const msg = err.response?.data?.message || err.response?.data?.errors?.domain?.[0] || "Ocurrió un error";
      Toast.fire({
        icon: "error",
        title: msg,
      });
    })
    .finally(() => {
      isSubmitting.value = false;
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
const heroBgLoaded = ref(false);
const HERO_BG_URL = "/assets/fonts/image/landing-back.png";

onMounted(() => {
  const img = new Image();
  img.decoding = "async";
  img.src = HERO_BG_URL;
  if (img.complete) {
    heroBgLoaded.value = true;
  } else {
    img.onload = () => {
      heroBgLoaded.value = true;
    };
  }
});

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
    <!-- Wrapper: hero (background + text) + form section (outside background on mobile) -->
    <div class="central-landing-wrapper">
      <!-- Hero: only background + text (on mobile). On desktop, form-outer overlaps left. -->
      <div class="central-landing-hero">
        <div class="central-landing-hero__bg" aria-hidden="true">
          <div class="central-landing-hero__bg-fallback"></div>
          <img
            :src="HERO_BG_URL"
            alt=""
            class="central-landing-hero__bg-img"
            :class="{ 'is-loaded': heroBgLoaded }"
            decoding="async"
            fetchpriority="high"
            @load="heroBgLoaded = true"
          />
        </div>
        <div class="central-landing-hero__overlay"></div>
        <div class="central-landing-hero__content">
          <div class="central-landing-hero__container">
            <div class="row align-items-center g-4 py-5 central-landing-hero__row">
              <!-- Spacer on desktop so text doesn't sit under form -->
              <div class="col-lg-5 col-xl-4 d-none d-lg-block"></div>
              <!-- Hero text only (offset on desktop = original position) -->
              <div class="col-12 col-lg-6 col-xl-6 offset-lg-1 offset-xl-1 central-landing-hero__text">
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

      <!-- Form: inside hero on desktop (absolute), below hero on mobile (own section, no background) -->
      <div class="central-landing-form-outer">
        <div class="central-landing-hero__container">
          <div class="row">
            <div class="col-12 col-lg-5 col-xl-4">
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
                        isSubmitting ||
                        !form.cct ||
                        !form.school_name ||
                        !form.email ||
                        !form.password ||
                        !form.phone
                      "
                    >
                      <i v-if="isSubmitting" class="fa-solid fa-circle-notch fa-spin me-2"></i>
                      <i v-else class="fa-solid fa-user-plus me-2"></i>
                      {{ isSubmitting ? "Creando cuenta..." : "Crear Cuenta Gratuita" }}
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

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
/* Wrapper: desktop = form overlaps hero; mobile = form section below hero */
.central-landing-wrapper {
  position: relative;
}

.central-landing-hero {
  position: relative;
  min-height: 100vh;
  display: flex;
  align-items: center;
  overflow: hidden;
  padding-top: 0;
  margin-top: 0;
}

.central-landing-hero__bg {
  position: absolute;
  inset: 0;
  z-index: 0;
}

.central-landing-hero__bg-fallback {
  position: absolute;
  inset: 0;
  background:
    radial-gradient(circle at 20% 20%, rgba(99, 91, 255, 0.28), transparent 42%),
    linear-gradient(135deg, #0a2540 0%, #1a365d 50%, #243b53 100%);
}

.central-landing-hero__bg-img {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
  opacity: 0;
  transition: opacity 0.35s ease;
}

.central-landing-hero__bg-img.is-loaded {
  opacity: 1;
}

/* Form: on desktop positioned inside hero (left); on mobile below hero in its own section */
.central-landing-form-outer {
  /* mobile: default flow below hero */
}

.central-landing-form-outer .central-landing-form-card {
  margin-top: 0;
}

.central-landing-hero__overlay {
  position: absolute;
  inset: 0;
  z-index: 1;
  background: linear-gradient(
    90deg,
    rgba(10, 37, 64, 0.82) 0%,
    rgba(10, 37, 64, 0.35) 45%,
    rgba(10, 37, 64, 0.35) 55%,
    rgba(10, 37, 64, 0.82) 100%
  );
  pointer-events: none;
}

.central-landing-hero__content {
  position: relative;
  z-index: 2;
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
  background: rgba(255, 255, 255, 0.12);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.18);
  border-radius: 9999px;
}

.central-landing-hero__title {
  font-size: clamp(1.75rem, 4vw, 2.75rem);
  font-weight: 700;
  color: #fff;
  letter-spacing: -0.03em;
  line-height: 1.15;
  max-width: 100%;
}

.central-landing-hero__subtitle {
  font-size: clamp(1rem, 1.5vw, 1.125rem);
  color: rgba(255, 255, 255, 0.82);
  line-height: 1.65;
  max-width: 100%;
  margin: 0;
}

/* Compact form card on the side */
.central-landing-form-card {
  background: #ffffff;
  border-radius: 1rem;
  box-shadow: 0 18px 45px rgba(10, 37, 64, 0.18);
  border: 1px solid rgba(255, 255, 255, 0.65);
  overflow: hidden;
  max-width: 100%;
  margin: 0 auto;
  margin-top: 80px;
}

.central-landing-form-card__header {
  padding: 1.25rem 1.75rem 1rem;
  border-bottom: 1px solid #e6ebf1;
  background: #ffffff;
}

.central-landing-form-card__title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #0a2540;
  margin: 0 0 0.25rem 0;
  letter-spacing: -0.02em;
}

.central-landing-form-card__subtitle {
  font-size: 0.875rem;
  color: #697386;
  margin: 0;
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
  border: 1px solid #e6ebf1;
  background: #ffffff;
  font-size: 0.95rem;
  padding: 1.5rem 0.875rem 0.5rem;
  min-height: 3.5rem;
  color: #0a2540;
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
  border-color: #635bff;
  background: #fff;
  box-shadow: 0 0 0 3px rgba(99, 91, 255, 0.18);
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
  padding: 0.75rem 1.25rem;
  font-weight: 600;
  font-size: 1rem;
  background: #635bff;
  border: none;
  border-radius: 0.5rem;
  color: #fff !important;
  box-shadow: none;
  transition: background 0.15s ease, transform 0.1s ease;
}

.central-landing-form__submit:hover:not(:disabled) {
  background: #5851ea;
  color: #fff !important;
  transform: translateY(-1px);
}

.central-landing-form__submit:disabled {
  opacity: 0.55;
  cursor: not-allowed;
  background: #a3acb9;
  color: #fff !important;
}

/* Desktop: form overlaps hero on the left */
@media (min-width: 992px) {
  .central-landing-form-outer {
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    transform: translateY(-50%);
    z-index: 3;
    pointer-events: none;
  }
  .central-landing-form-outer .central-landing-hero__container,
  .central-landing-form-outer .row,
  .central-landing-form-outer .col-12,
  .central-landing-form-outer .central-landing-form-card {
    pointer-events: auto;
  }
  .central-landing-form-outer .central-landing-form-card {
    margin-top: 80px;
  }
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
  /* Mobile: hero = full viewport height so background fills entire screen; form below on scroll */
  .central-landing-hero {
    min-height: 100vh;
    min-height: 100dvh;
    align-items: center;
    padding-top: 14rem !important;
    padding-bottom: 3rem !important;
    box-sizing: border-box;
  }
  .central-landing-hero__content {
    align-items: center;
    flex: 1;
  }
  .central-landing-hero__row {
    align-items: center;
    width: 100%;
  }
  .central-landing-hero__text {
    text-align: top;
    margin-bottom: 0;
  }
  /* Form section: outside hero, own background so hero image is fully visible above */
  .central-landing-form-outer {
    padding: 1.5rem 0 2.5rem;
    background: #cff3f8;
  }
  .central-landing-form-outer .central-landing-form-card {
    max-width: 540px;
    margin-left: auto;
    margin-right: auto;
    background: rgba(49, 63, 71, 0.95);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12), 0 0 0 1px rgba(0, 0, 0, 0.06);
  }
  .central-landing-form-outer .central-landing-form-card__header {
    background: rgba(54, 255, 245, 0.6);
    border-bottom-color: rgba(0, 0, 0, 0.08);
  }
  .central-landing-form-outer .central-landing-form-card__title {
    color: #f8f8f8;
    text-shadow: none;
  }
  .central-landing-form-outer .central-landing-form-card__subtitle {
    color: #ffffff;
    text-shadow: none;
  }
  .hero-text-offset {
    margin-left: 0;
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
}

@media (max-width: 575.98px) {
  .central-landing-hero {
    padding-top: 19rem !important;
    padding-bottom: 3rem !important;
  }
  .central-landing-form-outer {
    padding: 1.25rem 0 2rem;
  }
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
#page-footer .content {
  max-width: 1320px !important;
  margin-left: auto !important;
  margin-right: auto !important;
  padding-left: 1.5rem !important;
  padding-right: 1.5rem !important;
}

@media (min-width: 1400px) {
  #page-footer .content {
    max-width: 1400px !important;
  }
}

#page-footer .py-5 {
  padding-top: 2rem !important;
  padding-bottom: 2rem !important;
}
</style>
