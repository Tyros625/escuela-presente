import { reactive, computed, onMounted } from "vue";

export function useDataTable() {
  // Helper variables
  const cols = reactive([]);
  // Sort by functionality
  const sortBy = computed(() => {
    return cols.reduce((acc, o) => {
      if (o.sort) {
        o.sort === "asc" ? acc.push(o.field) : acc.push("-" + o.field);
      }
      return acc;
    }, []);
  });
  // On sort th click
  function onSort(event, i) {
    let toset;
    const sortEl = cols[i];

    if (!event.shiftKey) {
      cols.forEach((o) => {
        if (o.field !== sortEl.field) {
          o.sort = "";
        }
      });
    }

    if (!sortEl.sort) {
      toset = "asc";
    }

    if (sortEl.sort === "desc") {
      toset = event.shiftKey ? "" : "asc";
    }

    if (sortEl.sort === "asc") {
      toset = "desc";
    }

    sortEl.sort = toset;
  }

  onMounted(() => {
    // Remove labels from
    document.querySelectorAll("#datasetLength label").forEach((el) => {
      el.remove();
    });

    // Replace select classes
    let selectLength = document.querySelector("#datasetLength select");

    selectLength.classList = "";
    selectLength.classList.add("form-select");
    selectLength.style.width = "80px";
  });

  return {
    cols,
    sortBy,
    onSort,
  };
}
