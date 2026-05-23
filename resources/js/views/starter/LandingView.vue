<script setup></script>

<template>
  <!-- Hero with background image -->
  <div class="landing-hero">
    <div class="landing-hero__overlay"></div>
    <div class="landing-hero__content content content-full">
      <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7 text-center">
          <div class="landing-hero__badge mb-3">
            <i class="fa-solid fa-graduation-cap me-2"></i>
            <span>Gestión escolar en la nube</span>
          </div>
          <h1 class="landing-hero__title mb-3">
            Escuela
            <span class="landing-hero__title--light">Presente</span>
          </h1>
          <p class="landing-hero__subtitle mb-4">
            Administra tu institución educativa de forma simple y segura.
            Asistencia, calificaciones y comunicación en un solo lugar.
          </p>
          <RouterLink
            :to="{ name: 'dashboard' }"
            class="btn btn-primary landing-hero__cta px-4 py-3"
          >
            Ingresar
            <i class="fa fa-fw fa-arrow-right ms-2"></i>
          </RouterLink>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.landing-hero {
  position: relative;
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background:
    radial-gradient(circle at 20% 20%, rgba(99, 91, 255, 0.35), transparent 42%),
    radial-gradient(circle at 80% 0%, rgba(56, 189, 248, 0.18), transparent 35%),
    linear-gradient(135deg, #0a2540 0%, #1a365d 48%, #243b53 100%);
}

.landing-hero__overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    135deg,
    rgba(15, 23, 42, 0.85) 0%,
    rgba(30, 41, 59, 0.75) 50%,
    rgba(15, 23, 42, 0.85) 100%
  );
  pointer-events: none;
}

.landing-hero__content {
  position: relative;
  z-index: 1;
  padding: 2rem 1rem;
}

.landing-hero__badge {
  display: inline-flex;
  align-items: center;
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
  font-weight: 500;
  color: rgba(255, 255, 255, 0.95);
  background: rgba(255, 255, 255, 0.12);
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 9999px;
}

.landing-hero__title {
  font-size: clamp(2.25rem, 5vw, 3.5rem);
  font-weight: 700;
  color: #fff;
  letter-spacing: -0.02em;
  line-height: 1.15;
  text-shadow: 0 2px 20px rgba(0, 0, 0, 0.2);
}

.landing-hero__title--light {
  font-weight: 300;
  opacity: 0.95;
}

.landing-hero__subtitle {
  font-size: 1.125rem;
  font-weight: 400;
  color: rgba(255, 255, 255, 0.9);
  max-width: 32rem;
  margin-left: auto;
  margin-right: auto;
  line-height: 1.6;
  text-shadow: 0 1px 8px rgba(0, 0, 0, 0.15);
}

.landing-hero__cta {
  font-weight: 600;
  font-size: 1rem;
  padding: 0.875rem 1.75rem;
  background: #0ea5e9;
  border: none;
  border-radius: 0.5rem;
  color: #fff;
  box-shadow: 0 4px 14px rgba(14, 165, 233, 0.4);
  transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
}

.landing-hero__cta:hover {
  background: #0284c7;
  color: #fff;
  transform: translateY(-1px);
  box-shadow: 0 6px 20px rgba(14, 165, 233, 0.5);
}
</style>
