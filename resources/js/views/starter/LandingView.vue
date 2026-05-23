<script setup>
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
</script>

<template>
  <div class="landing-hero">
    <div class="landing-hero__bg" aria-hidden="true">
      <div class="landing-hero__bg-fallback"></div>
      <img
        :src="HERO_BG_URL"
        alt=""
        class="landing-hero__bg-img"
        :class="{ 'is-loaded': heroBgLoaded }"
        decoding="async"
        fetchpriority="high"
        @load="heroBgLoaded = true"
      />
    </div>
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
          <RouterLink :to="{ name: 'dashboard' }" class="btn btn-primary landing-hero__cta px-4 py-3">
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
  overflow: hidden;
}

.landing-hero__bg {
  position: absolute;
  inset: 0;
  z-index: 0;
}

.landing-hero__bg-fallback {
  position: absolute;
  inset: 0;
  background:
    radial-gradient(circle at 20% 20%, rgba(99, 91, 255, 0.28), transparent 42%),
    linear-gradient(135deg, #0a2540 0%, #1a365d 50%, #243b53 100%);
}

.landing-hero__bg-img {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
  opacity: 0;
  transition: opacity 0.35s ease;
}

.landing-hero__bg-img.is-loaded {
  opacity: 1;
}

.landing-hero__overlay {
  position: absolute;
  inset: 0;
  z-index: 1;
  background: linear-gradient(
    135deg,
    rgba(10, 37, 64, 0.78) 0%,
    rgba(10, 37, 64, 0.55) 50%,
    rgba(10, 37, 64, 0.78) 100%
  );
  pointer-events: none;
}

.landing-hero__content {
  position: relative;
  z-index: 2;
  padding: 2rem 1rem;
}

.landing-hero__badge {
  display: inline-flex;
  align-items: center;
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
  font-weight: 600;
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
  letter-spacing: -0.03em;
  line-height: 1.12;
}

.landing-hero__title--light {
  font-weight: 500;
  opacity: 0.95;
}

.landing-hero__subtitle {
  font-size: 1.125rem;
  font-weight: 500;
  color: rgba(255, 255, 255, 0.88);
  max-width: 32rem;
  margin-left: auto;
  margin-right: auto;
  line-height: 1.65;
}

.landing-hero__cta {
  font-weight: 600;
  font-size: 1rem;
  border-radius: 0.5rem;
}
</style>
