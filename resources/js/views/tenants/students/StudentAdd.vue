<template>
	<BasePageHeading title="Estudiantes">
		<template #extra>
			<router-link :to="{ name: 'students' }">
				<button type="button" class="btn btn-alt-primary" v-click-ripple>
					<i class="fa fa-arrow-left-long opacity-50 me-1"></i>
					Regresar
				</button>
			</router-link>
		</template>
	</BasePageHeading>

	<div class="content">
		<h2 class="content-heading">Agregar Estudiante</h2>
		<ErrorsView v-if="errors.length" :errors="errors" />
		<BaseBlock>
			<template #content>
				<ul class="nav nav-tabs nav-tabs-block" role="tablist">
					<li class="nav-item">
						<button
							class="nav-link active"
							id="btabs-1"
							data-bs-toggle="tab"
							data-bs-target="#btabs-static-1"
							role="tab"
							aria-controls="btabs-static-1"
							aria-selected="true"
						>
							PERSONALES
						</button>
					</li>
					<li class="nav-item">
						<button
							class="nav-link"
							id="btabs-2"
							data-bs-toggle="tab"
							data-bs-target="#btabs-static-2"
							role="tab"
							aria-controls="btabs-static-2"
							aria-selected="false"
						>
							Académicos
						</button>
					</li>
					<li class="nav-item">
						<button
							class="nav-link"
							id="btabs-3"
							data-bs-toggle="tab"
							data-bs-target="#btabs-static-3"
							role="tab"
							aria-controls="btabs-static-3"
							aria-selected="false"
						>
							Familiares
						</button>
					</li>
					<li class="nav-item">
						<button
							class="nav-link"
							id="btabs-4"
							data-bs-toggle="tab"
							data-bs-target="#btabs-static-4"
							role="tab"
							aria-controls="btabs-static-4"
							aria-selected="false"
						>
							SocioEconómicos
						</button>
					</li>
					<li class="nav-item">
						<button
							class="nav-link"
							id="btabs-5"
							data-bs-toggle="tab"
							data-bs-target="#btabs-static-5"
							role="tab"
							aria-controls="btabs-static-5"
							aria-selected="false"
						>
							Salud e Higiene
						</button>
					</li>
					<li class="nav-item">
						<button
							class="nav-link"
							id="btabs-6"
							data-bs-toggle="tab"
							data-bs-target="#btabs-static-6"
							role="tab"
							aria-controls="btabs-static-6"
							aria-selected="false"
						>
							Cuidados Médicos
						</button>
					</li>
				</ul>
				<div class="block-content tab-content">
					<div
						class="tab-pane active"
						id="btabs-static-1"
						role="tabpanel"
						aria-labelledby="btabs-1"
						tabindex="0"
					>
						<Tab1 />
					</div>
					<div
						class="tab-pane"
						id="btabs-static-2"
						role="tabpanel"
						aria-labelledby="btabs-2"
						tabindex="0"
					>
						<Tab2 />
					</div>
					<div
						class="tab-pane"
						id="btabs-static-3"
						role="tabpanel"
						aria-labelledby="btabs-3"
						tabindex="0"
					>
						<Tab3 />
					</div>
					<div
						class="tab-pane"
						id="btabs-static-4"
						role="tabpanel"
						aria-labelledby="btabs-4"
						tabindex="0"
					>
						<Tab4 />
					</div>
					<div
						class="tab-pane"
						id="btabs-static-5"
						role="tabpanel"
						aria-labelledby="btabs-5"
						tabindex="0"
					>
						<Tab5 />
					</div>
					<div
						class="tab-pane"
						id="btabs-static-6"
						role="tabpanel"
						aria-labelledby="btabs-6"
						tabindex="0"
					>
						<Tab6 />
					</div>
				</div>
			</template>
		</BaseBlock>
		<div class="text-end mb-4">
			<button type="button" class="btn btn-alt-primary" v-click-ripple @click="saveData">
				<i class="fa-solid fa-floppy-disk"></i> Guardar
			</button>
		</div>
	</div>
</template>

<script setup>
import { useStudentStore } from "@/stores/student";
import Tab1 from "./tabs/Tab1.vue";
import Tab2 from "./tabs/Tab2.vue";
import Tab3 from "./tabs/Tab3.vue";
import Tab4 from "./tabs/Tab4.vue";
import Tab5 from "./tabs/Tab5.vue";
import Tab6 from "./tabs/Tab6.vue";

onMounted(async () => {
	await studentStore.getGrades();
	await studentStore.getGroups();
	studentStore.paymentType = null;
});

const studentStore = useStudentStore();
const router = useRouter();
const errors = ref([]);

function saveData() {
	studentStore.isLoading = true;
	api
		.post(`/students`, studentStore.form)
		.then((res) => {
			Toast.fire({
				icon: "success",
				title: "Guardado Correctamente",
			});
			studentStore.isLoading = false;
			router.push({ name: "students" });
		})
		.catch((err) => {
			studentStore.isLoading = false;
			errors.value = [];
			Object.getOwnPropertyNames(err.data.errors).forEach(function (val) {
				err.data.errors[val].forEach((element) => {
					errors.value.push(element);
				});
			});

			Toast.fire({
				icon: "error",
				title: "Error",
			});
		});
}
</script>
