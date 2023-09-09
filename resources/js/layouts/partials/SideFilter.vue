<script setup>
import { onMounted, onUnmounted } from "vue";
import { useTemplateStore } from "@/stores/template";

// SimpleBar, for more info and examples you can check out https://github.com/Grsmto/simplebar/tree/master/packages/simplebar-vue
import SimpleBar from "simplebar";

// Main store
const store = useTemplateStore();

// Close side overlay on ESCAPE key down
function eventSideOverlay(event) {
  if (event.which === 27) {
    event.preventDefault();
    store.sideOverlay({ mode: "close" });
  }
}

// Init SimpleBar (custom scrolling) and attach ESCAPE key event listener
onMounted(() => {
  new SimpleBar(document.getElementById("side-overlay"));

  document.addEventListener("keydown", eventSideOverlay);
});

// Remove keydown event listener
onUnmounted(() => {
  document.removeEventListener("keydown", eventSideOverlay);
});
</script>

<template>
  <!-- Side Overlay-->
  <aside id="side-overlay">
    <slot>
      <!-- Side Header -->
      <div class="content-header border-bottom">
        <slot name="header">
          <!-- User Info -->
          <div class="mt-4">
            <h3>Filtros</h3>
          </div>
          <!-- END User Info -->
        </slot>

        <!-- Close Side Overlay -->
        <button type="button" class="ms-auto btn btn-sm btn-alt-danger" @click="store.sideOverlay({ mode: 'close' })">
          <i class="fa fa-fw fa-times"></i>
        </button>
        <!-- END Close Side Overlay -->
      </div>
      <!-- END Side Header -->

      <slot name="content">
        <!-- Side Content -->
        <div class="content-side">
          <!-- Side Overlay Tabs -->
          <BaseBlock transparent :rounded="false" class="pull-x pull-t">
            <template #content>
              <div class="container">
                <div class="row g-3">
                  <div class="col-md-12">
                    <label class="form-label">Fecha</label>
                    <input type="text" class="form-control" />
                  </div>
                  <div class="col-md-12">
                    <label class="form-label">Fecha</label>
                    <input type="text" class="form-control" />
                  </div>
                  <div class="d-grid gap-2">
                    <button class="btn btn-success rounded-pill" type="button">
                      <i class="fa-solid fa-filter"></i> Filtrar
                    </button>
                  </div>
                </div>
              </div>
            </template>
          </BaseBlock>
          <!-- END Side Overlay Tabs -->
        </div>
        <!-- END Side Content -->
      </slot>
    </slot>
  </aside>
  <!-- END Side Overlay -->
</template>
