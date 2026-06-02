import { ref } from "vue";

/** Center splash-style route progress (visible ~1–2s, no full-screen backdrop). */
export const routeProgressVisible = ref(false);
export const routeProgressValue = ref(0);

const MIN_DURATION_MS = 1500;
const HIDE_AFTER_COMPLETE_MS = 280;

let startedAt = 0;
let tickTimer = null;
let finishTimer = null;
let hideTimer = null;

function clearTimers() {
  if (tickTimer) {
    clearInterval(tickTimer);
    tickTimer = null;
  }
  if (finishTimer) {
    clearTimeout(finishTimer);
    finishTimer = null;
  }
  if (hideTimer) {
    clearTimeout(hideTimer);
    hideTimer = null;
  }
}

function shouldAnimate(from, to) {
  if (!to?.name) {
    return false;
  }

  return to.fullPath !== from.fullPath;
}

function startRouteProgress() {
  clearTimers();
  startedAt = Date.now();
  routeProgressVisible.value = true;
  routeProgressValue.value = 10;

  tickTimer = window.setInterval(() => {
    const elapsed = Date.now() - startedAt;
    const cap = elapsed < MIN_DURATION_MS * 0.75 ? 82 : 94;

    if (routeProgressValue.value < cap) {
      routeProgressValue.value = Math.min(
        cap,
        routeProgressValue.value + 2 + Math.random() * 5
      );
    }
  }, 100);
}

function finishRouteProgress() {
  if (tickTimer) {
    clearInterval(tickTimer);
    tickTimer = null;
  }

  const runComplete = () => {
    routeProgressValue.value = 100;

    hideTimer = window.setTimeout(() => {
      routeProgressVisible.value = false;
      routeProgressValue.value = 0;
      hideTimer = null;
    }, HIDE_AFTER_COMPLETE_MS);
  };

  const elapsed = Date.now() - startedAt;
  const wait = Math.max(0, MIN_DURATION_MS - elapsed);

  finishTimer = window.setTimeout(() => {
    runComplete();
    finishTimer = null;
  }, wait);
}

export function attachRouterPageTransition(router) {
  router.beforeEach((to, from, next) => {
    if (shouldAnimate(from, to)) {
      startRouteProgress();
    }
    next();
  });

  router.afterEach((to, from) => {
    if (!shouldAnimate(from, to)) {
      clearTimers();
      routeProgressVisible.value = false;
      routeProgressValue.value = 0;
      return;
    }

    finishRouteProgress();
  });
}
