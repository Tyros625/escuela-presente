import { ref } from "vue";

/** Full-screen blur + splash EP loader while the next route loads. */
export const routeProgressVisible = ref(false);

const MIN_DURATION_MS = 1200;
const HIDE_AFTER_COMPLETE_MS = 400;

let startedAt = 0;
let finishTimer = null;
let hideTimer = null;

function clearTimers() {
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

function setBodyLoading(active) {
  document.body.classList.toggle("ep-route-loading", active);
}

function startRouteProgress() {
  clearTimers();
  startedAt = Date.now();
  setBodyLoading(true);
  routeProgressVisible.value = true;
}

function finishRouteProgress() {
  const runComplete = () => {
    hideTimer = window.setTimeout(() => {
      routeProgressVisible.value = false;
      setBodyLoading(false);
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
      setBodyLoading(false);
      return;
    }

    finishRouteProgress();
  });
}
