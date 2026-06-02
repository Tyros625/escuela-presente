import NProgress from "nprogress/nprogress.js";

let finishTimer = null;

NProgress.configure({
  showSpinner: false,
  trickleSpeed: 90,
  minimum: 0.12,
  easing: "ease",
  speed: 320,
});

function shouldAnimate(from, to) {
  if (!to?.name) {
    return false;
  }

  return to.fullPath !== from.fullPath;
}

export function attachRouterPageTransition(router) {
  router.beforeEach((to, from, next) => {
    if (shouldAnimate(from, to)) {
      if (finishTimer) {
        clearTimeout(finishTimer);
        finishTimer = null;
      }
      NProgress.start();
    }
    next();
  });

  router.afterEach((to, from) => {
    if (!shouldAnimate(from, to)) {
      NProgress.done();
      return;
    }

    finishTimer = window.setTimeout(() => {
      NProgress.done();
      finishTimer = null;
    }, 140);
  });
}
