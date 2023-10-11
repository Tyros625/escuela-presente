<script setup>
import { useTemplateStore } from "@/stores/template";
import { useStudentStore } from "@/stores/student";
import Wizard from "form-wizard-vue3";
import Tab1 from "../tenants/students/tabs/Tab1.vue";
import Tab2 from "../tenants/students/tabs/Tab2.vue";
import Tab3 from "../tenants/students/tabs/Tab3.vue";
import Tab4 from "../tenants/students/tabs/Tab4.vue";
import Tab5 from "../tenants/students/tabs/Tab5.vue";
import Tab6 from "../tenants/students/tabs/Tab6.vue";
import Swal from "sweetalert2";

const script = document.createElement("script");
script.setAttribute("src", "https://sdk.mercadopago.com/js/v2");
document.head.appendChild(script);

onMounted(async () => {
	await studentStore.getGrades();
	await studentStore.getGroups();
});

const showSteps = ref(false);
const store = useTemplateStore();
const studentStore = useStudentStore();
const backButton = {
	text: "Anterior",
	hideIcon: false,
	hideText: false,
};
const nextButton = {
	text: "Siguiente",
	hideIcon: false,
	hideText: false,
};
const doneButton = {
	text: "Finalizar",
	hideIcon: false,
	hideText: false,
};
const customTabs = [
	{
		title: "Paso 1",
	},
	{
		title: "Paso 2",
	},
	{
		title: "Paso 3",
	},
	{
		title: "Paso 4",
	},
	{
		title: "Paso 5",
	},
	{
		title: "Paso 6",
	},
];
const currentTabIndex = ref(0);

function onChangeCurrentTab(index, oldIndex) {
	//console.log(index, oldIndex);
	currentTabIndex.value = index;
}

function onTabBeforeChange() {
	if (currentTabIndex.value === 0) {
		//console.log("First Tab");
	}
	//console.log("All Tabs");
}

const errors = ref([]);

const wizardCompleted = async () => {
	studentStore.isLoading = true;

	try {
		const { data } = await api.post(`/students`, studentStore.form);
		studentStore.studentId = data.id;
		studentStore.isLoading = false;
	} catch (error) {
		errors.value = [];
		studentStore.isLoading = false;

		Object.getOwnPropertyNames(error.data.errors).forEach(function (val) {
			error.data.errors[val].forEach((element) => {
				errors.value.push(element);
			});
		});
	}
};

async function showModal() {
	if (studentStore.paymentType != "NUEVO INGRESO") {
		const { value: curp } = await Swal.fire({
			title: "Ingrese su CURP",
			input: "text",
			inputLabel: "CURP del Alumno",
			inputPlaceholder: "Ingrese el CURP del alumno",
		});

		if (curp) {
			console.log(curp);
			try {
				const { data } = await api.get(`/students/by-curp/${curp}`);
				studentStore.form = data.data;

				if (studentStore.paymentType === "REINSCRIPCIÓN") {
					let condition = await verifyGrade();
					if (!condition)
						return Swal.fire("Error", "No hay mas grados donde reinscribirse", "error");
				}

				Swal.fire(
					`Hola ${data.data.name}.`,
					"Válida que tus datos sean los correctos. Si hay alguna modificación o actualización que desees realizar puedes hacerlo.",
					"warning"
				);

				//Swal.fire(`Hola ${data.data.name}. Valida`);
				showSteps.value = true;
			} catch (error) {
				Swal.fire(`CURP ${curp} no encontrado`);
			}
		}
	} else {
		showSteps.value = true;
	}
}

function searchSection() {
	let studentsFilter = studentStore.groups.filter(
		(a) => a.grade === studentStore.form.grade
	);
	studentStore.form.academic_group_id = studentsFilter[0].id;
}

const verifyGrade = async () => {
	if (
		studentStore.grades[studentStore.grades.length - 1].description ===
		studentStore.form.grade
	) {
		return false;
	}

	//ACTUALIZAR NUEVO GRADO
	let actualGrade = studentStore.grades.find(
		(el) => el.description === studentStore.form.grade
	);

	let siguiente = studentStore.grades.find((el) => el.order === actualGrade.order + 1);
	studentStore.form.grade = siguiente.description;

	searchSection();

	return true;
};
</script>

<template>
	<div>
		<div id="one-vue-versions" class="bg-body-light">
			<div class="content content-full">
				<div class="py-5">
					<div class="row mb-5">
						<div class="col-md-12 text-center">
							<h2 class="h1 fw-bold mb-2">Registro de Alumnos</h2>
						</div>
					</div>
				</div>
				<div class="row" v-if="!showSteps">
					<div class="col-12">
						<BaseBlock transparent class="bg-success-light">
							<div class="text-center text-sucess fw-bold mb-4">
								<h4>SELECCIONE LA ACCIÓN QUE DESEA REALIZAR:</h4>
								<div class="row">
									<div class="col-md-12">
										<div class="mb-4">
											<label class="form-label"> Seleccione la Opción Deseada: </label>
											<select
												class="form-select"
												v-model="studentStore.paymentType"
												:disabled="studentStore.isLoading"
											>
												<option v-for="item in studentStore.paymentTypeOptions">
													{{ item }}
												</option>
											</select>
										</div>
									</div>
								</div>

								<button
									type="button"
									class="btn btn-primary mt-2"
									:disabled="studentStore.isLoading"
									@click="showModal"
								>
									Siguiente
									<i class="fa-solid fa-caret-right"></i>
								</button>
							</div>
						</BaseBlock>
					</div>
				</div>
				<div class="row" v-else>
					<div class="col-12">
						<ErrorsView v-if="errors.length" :errors="errors" />
						<BaseBlock transparent class="bg-flat-lighter">
							<div class="text-center text-danger fw-bold">
								<h4>
									LEA CON ATENCIÓN, LLENE Y/O COMPLETE LOS SIGUIENTES CAMPOS.<br />
									UTILICE UN CORREO ELECTRÓNICO PERSONAL VIGENTE. <br />
									LE LLEGARÁ EL COMPROBANTE DE SU SOLICITUD.
								</h4>
							</div>
							<Wizard
								class="mb-4"
								squared-tabs
								card-background
								navigable-tabs
								scrollable-tabs
								:backButton="backButton"
								:nextButton="nextButton"
								:doneButton="doneButton"
								:custom-tabs="customTabs"
								:beforeChange="onTabBeforeChange"
								@change="onChangeCurrentTab"
								@complete:wizard="wizardCompleted"
							>
								<!-- STEP 1 -->
								<Tab1 v-if="currentTabIndex === 0" />
								<!-- STEP 2 -->
								<Tab2 v-if="currentTabIndex === 1" />
								<!-- STEP 3 -->
								<Tab3 v-if="currentTabIndex === 2" />
								<!-- STEP 4 -->
								<Tab4 v-if="currentTabIndex === 3" />
								<!-- STEP 5 -->
								<Tab5 v-if="currentTabIndex === 4" />
								<!-- STEP 6 -->
								<Tab6 v-if="currentTabIndex === 5" />
							</Wizard>
						</BaseBlock>
					</div>
				</div>
			</div>
		</div>
		<!-- END Call To Action -->

		<!-- Footer -->
		<footer id="page-footer" class="bg-body-light">
			<div class="content py-5">
				<div class="row fs-sm fw-medium">
					<div class="col-sm-6 order-sm-2 py-1 text-center text-sm-end">
						Desarrollado
						<i class="fa fa-heart text-danger"></i> por
						<a class="fw-semibold" href="https://hacktrick.tech"> HackTrick </a>
					</div>
					<div class="col-sm-6 order-sm-1 py-1 text-center text-sm-start">
						<a class="fw-semibold" href="https://1.envato.market/5Noyb">
							{{ store.app.name + " " + store.app.version }}
						</a>
						&copy; {{ store.app.copyright }}
					</div>
				</div>
			</div>
		</footer>
		<!-- END Footer -->
	</div>
</template>

<style lang="scss">
@import "form-wizard-vue3/dist/form-wizard-vue3.css";
</style>
