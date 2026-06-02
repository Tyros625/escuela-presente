import { createRouter, createWebHashHistory } from "vue-router";

import { attachRouterPageTransition } from "@/router/pageTransition";

// Main layouts
import LayoutBackend from "@/layouts/variations/Backend.vue";
import LayoutSimple from "@/layouts/variations/Simple.vue";
import LayoutLanding from "@/layouts/variations/Landing.vue";
import LayoutOxxo from "@/layouts/OxxoLayout.vue";

import { useUserStore } from "@/stores/user";

// Frontend: Landing
const Landing = () => import("@/views/starter/LandingView.vue");
const StudentRegister = () => import("@/views/starter/StudentRegisterView.vue");

// Backend: Dashboard
const Dashboard = () => import("@/views/starter/DashboardTenant.vue");

//Configuration
const GeneralConfig = () => import("@/views/tenants/GeneralConfig.vue");
const AccountConfig = () => import("@/views/starter/AccountConfig.vue");

const PaymentSuccessView = () =>
  import("@/views/tenants/PaymentSuccessView.vue");
const PaymentFailureView = () =>
  import("@/views/tenants/PaymentFailureView.vue");
const PaymentPendingView = () =>
  import("@/views/tenants/PaymentPendingView.vue");

// Auth Views
const AuthSignIn = () => import("@/views/auth/SignIn3View.vue");
const AuthReminder = () => import("@/views/auth/ReminderView.vue");
const AuthSignUp = () => import("@/views/auth/SignUpView.vue");

// Users
const UserIndex = () => import("@/views/users/UserIndex.vue");
const UserAdd = () => import("@/views/users/UserAdd.vue");
const UserEdit = () => import("@/views/users/UserEdit.vue");

//Students
const StudentIndex = () => import("@/views/tenants/students/StudentIndex.vue");
const StudentAdd = () => import("@/views/tenants/students/StudentAdd.vue");
const StudentEdit = () => import("@/views/tenants/students/StudentEdit.vue");
const StudentDetail = () =>
  import("@/views/tenants/students/StudentDetail.vue");
const StudentIncident = () =>
  import("@/views/tenants/students/StudentIncident.vue");

// Assists
const AssistsIndex = () => import("@/views/tenants/assists/AssistView.vue");
const AssistDinner = () => import("@/views/tenants/assists/AssistDinner.vue");
const PrintAssist = () => import("@/views/tenants/assists/PrintAssist.vue");
const DinnerIndex = () => import("@/views/tenants/dinner/IndexView.vue");

//Maestros
const TeacherView = () => import("@/views/tenants/teachers/TeacherView.vue");
const SubjectAssignmentView = () =>
  import("@/views/tenants/teachers/SubjectAssignmentView.vue");
const TeachingAssignmentView = () =>
  import("@/views/tenants/teachers/TeachingAssignmentView.vue");
const SpecialtyView = () =>
  import("@/views/tenants/specialties/SpecialtyView.vue");
const IncidentView = () => import("@/views/tenants/incidents/IncidentView.vue");
const GradeView = () => import("@/views/tenants/grades/GradeView.vue");
const QualificationRecordView = () =>
  import("@/views/tenants/grades/QualificationRecordView.vue");
const SectionView = () => import("@/views/tenants/sections/SectionView.vue");
const SchoolCycleView = () =>
  import("@/views/tenants/school-cycles/SchoolCycleView.vue");
const AcademicGroupView = () =>
  import("@/views/tenants/academic-groups/AcademicGroupView.vue");

// Roles
const RoleIndex = () => import("@/views/roles/RoleIndex.vue");
const RoleAdd = () => import("@/views/roles/RoleAdd.vue");
const RoleEdit = () => import("@/views/roles/RoleEdit.vue");
const RoleDetail = () => import("@/views/roles/RoleDetail.vue");

//Payments
const PaymentView = () => import("@/views/tenants/payments/PaymentView.vue");

// Reports
const AssistsReport = () => import("@/views/tenants/reports/AssistsReport.vue");
const TardinessReport = () =>
  import("@/views/tenants/reports/TardinessReport.vue");
const IncidentReportIndex = () =>
  import("@/views/tenants/incident-reports/IncidentReportIndex.vue");
const IncidentReportAdd = () =>
  import("@/views/tenants/incident-reports/IncidentReportAdd.vue");

//Default
const GenericBlankBlock = () =>
  import("@/views/backend/pages/generic/BlankBlockView.vue");
const BackendPagesGenericProfile = () =>
  import("@/views/backend/pages/generic/ProfileView.vue");

const requireAuth = async (to, from, next) => {
  const userStore = useUserStore();
  let isAuth = userStore.isLoggedIn;

  if (!isAuth) {
    next("/login");
  }

  next();
};

// Set all routes
const routes = [
  {
    path: "/",
    component: LayoutSimple,
    children: [
      {
        path: "",
        name: "landing",
        component: Landing,
      },
      {
        path: "payment/success",
        name: "payment-success",
        component: PaymentSuccessView,
      },
      {
        path: "payment/failure",
        name: "payment-failure",
        component: PaymentFailureView,
      },
      {
        path: "payment/pending",
        name: "payment-pending",
        component: PaymentPendingView,
      },
    ],
  },
  {
    path: "/registro",
    component: LayoutLanding,
    children: [
      {
        path: "",
        name: "register",
        component: StudentRegister,
      },
    ],
  },
  {
    path: "/payment",
    name: "payment",
    component: LayoutOxxo,
  },
  {
    path: "/login",
    component: LayoutSimple,
    children: [
      {
        path: "",
        name: "auth-signin",
        component: AuthSignIn,
      },
      {
        path: "reminder",
        name: "auth-reminder",
        component: AuthReminder,
      },
      {
        path: "signup",
        name: "auth-signup",
        component: AuthSignUp,
      },
    ],
  },
  {
    path: "/",
    component: LayoutBackend,
    children: [
      {
        path: "dashboard",
        name: "dashboard",
        component: Dashboard,
        beforeEnter: requireAuth,
      },
      {
        path: "users",
        name: "users",
        component: UserIndex,
        beforeEnter: requireAuth,
      },
      {
        path: "users/add",
        name: "users.add",
        component: UserAdd,
        beforeEnter: requireAuth,
      },
      {
        path: "users/:id/edit",
        name: "users.edit",
        component: UserEdit,
        beforeEnter: requireAuth,
      },
      {
        path: "roles",
        name: "roles",
        component: RoleIndex,
        beforeEnter: requireAuth,
      },
      {
        path: "roles/add",
        name: "roles.add",
        component: RoleAdd,
        beforeEnter: requireAuth,
      },
      {
        path: "roles/:id/edit",
        name: "roles.edit",
        component: RoleEdit,
        beforeEnter: requireAuth,
      },
      {
        path: "roles/:id/detail",
        name: "roles.detail",
        component: RoleDetail,
        beforeEnter: requireAuth,
      },
      {
        path: "general-config",
        name: "general-config",
        component: GeneralConfig,
        beforeEnter: requireAuth,
      },
      {
        path: "account-config",
        name: "account-config",
        component: AccountConfig,
        beforeEnter: requireAuth,
      },
      {
        path: "profile",
        name: "profile",
        component: BackendPagesGenericProfile,
        beforeEnter: requireAuth,
      },
      {
        path: "blank-page",
        name: "blank-page",
        component: GenericBlankBlock,
        beforeEnter: requireAuth,
      },
      //Estudiantes
      {
        path: "students",
        name: "students",
        component: StudentIndex,
        beforeEnter: requireAuth,
      },
      {
        path: "students/add",
        name: "students.add",
        component: StudentAdd,
        beforeEnter: requireAuth,
      },
      {
        path: "students/:id/edit",
        name: "students.edit",
        component: StudentEdit,
        beforeEnter: requireAuth,
      },
      {
        path: "students/:id/detail",
        name: "students.detail",
        component: StudentDetail,
        beforeEnter: requireAuth,
      },
      {
        path: "students/:id/incident",
        name: "students.incident",
        component: StudentIncident,
        beforeEnter: requireAuth,
      },
      {
        path: "assists",
        name: "assists",
        component: AssistsIndex,
        beforeEnter: requireAuth,
      },
      {
        path: "assists-dinner",
        name: "assists-dinner",
        component: AssistDinner,
        beforeEnter: requireAuth,
      },
      {
        path: "dinners",
        name: "dinners",
        component: DinnerIndex,
        beforeEnter: requireAuth,
      },
      //Datos Maestros
      {
        path: "teachers",
        name: "teachers",
        component: TeacherView,
        beforeEnter: requireAuth,
      },
      {
        path: "subject-assignments",
        name: "subject-assignments",
        component: SubjectAssignmentView,
        beforeEnter: requireAuth,
      },
      {
        path: "teaching-assignments",
        name: "teaching-assignments",
        component: TeachingAssignmentView,
        beforeEnter: requireAuth,
      },
      {
        path: "specialties",
        name: "specialties",
        component: SpecialtyView,
        beforeEnter: requireAuth,
      },
      {
        path: "incidents",
        name: "incidents",
        component: IncidentView,
        beforeEnter: requireAuth,
      },
      {
        path: "reports/assists",
        name: "reports.assists",
        component: AssistsReport,
        beforeEnter: requireAuth,
      },
      {
        path: "reports/tardiness",
        name: "reports.tardiness",
        component: TardinessReport,
        beforeEnter: requireAuth,
      },
      {
        path: "reports/incidents",
        name: "reports.incidents",
        component: IncidentReportIndex,
        beforeEnter: requireAuth,
      },
      {
        path: "reports/incidents/add",
        name: "incident-reports.add",
        component: IncidentReportAdd,
        beforeEnter: requireAuth,
      },
      {
        path: "reports/incidents/:id/edit",
        name: "incident-reports.edit",
        component: IncidentReportAdd,
        beforeEnter: requireAuth,
      },
      {
        path: "payments",
        name: "payments",
        component: PaymentView,
        beforeEnter: requireAuth,
      },
      {
        path: "grades",
        name: "grades",
        component: GradeView,
        beforeEnter: requireAuth,
      },
      {
        path: "grades/qualification-record",
        name: "grades.qualification-record",
        component: QualificationRecordView,
        beforeEnter: requireAuth,
      },
      {
        path: "sections",
        name: "sections",
        component: SectionView,
        beforeEnter: requireAuth,
      },
      {
        path: "school-cycles",
        name: "school-cycles",
        component: SchoolCycleView,
        beforeEnter: requireAuth,
      },
      {
        path: "academic-groups",
        name: "academic-groups",
        component: AcademicGroupView,
        beforeEnter: requireAuth,
      },
    ],
  },
  {
    path: "/print",
    name: "print",
    component: PrintAssist,
  },
];

// Create Router
const router = createRouter({
  history: createWebHashHistory(),
  linkActiveClass: "active",
  linkExactActiveClass: "active",
  scrollBehavior() {
    return { left: 0, top: 0 };
  },
  routes,
});

attachRouterPageTransition(router);

export default router;
