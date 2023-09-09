import { onMounted, onUnmounted } from "vue";

export function useTooltip() {
  // Helper variables
  let tooltipTriggerList = [];
  let tooltipList = [];

  // Init tooltips on content loaded
  onMounted(() => {
    // Grab all tooltip containers..
    tooltipTriggerList = [].slice.call(
      document.querySelectorAll('[data-bs-toggle="tooltip"]')
    );

    // ..and init them
    tooltipList = tooltipTriggerList.map((tooltipTriggerEl) => {
      return new window.bootstrap.Tooltip(tooltipTriggerEl, {
        container: tooltipTriggerEl.dataset.bsContainer || "#page-container",
        animation:
          tooltipTriggerEl.dataset.bsAnimation &&
          tooltipTriggerEl.dataset.bsAnimation.toLowerCase() == "true"
            ? true
            : false,
      });
    });
  });

  // Dispose tooltips on unMounted
  onUnmounted(() => {
    tooltipList.forEach((tooltip) => tooltip.dispose());
  });
}
