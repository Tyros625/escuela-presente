/**
 * Menu structure by role (wireframe: Administrador, Docente, Estudiante, Padre/Tutor).
 * Existing routes are linked; missing features are placeholders (to: '#' or blank-page).
 */
function sub(to, name, show = true) {
  return { to, name, attributes: { show } };
}

function item(name, icon, to, show = true) {
  return { name, icon, to, attributes: { show } };
}

function group(name, icon, subItems, show = true) {
  return {
    name,
    icon,
    attributes: { show },
    sub: subItems,
  };
}

/** Map role name from API to menu key */
export function roleToMenuKey(roleName) {
  if (!roleName) return "admin";
  const r = String(roleName).toLowerCase();
  if (r === "administrador" || r === "admin" || r === "super admin") return "admin";
  if (r === "docente") return "teacher";
  if (r === "estudiante") return "student";
  if (r === "padre/tutor" || r === "padre") return "parent";
  if (r === "usuario") return "student"; // legacy
  return "admin";
}

/** Menu nodes for Administrador (full admin) */
export function menuAdmin(userStore) {
  const can = (p) => userStore.userCan(p);
  return [
    item("Dashboard", "fa fa-gauge", "dashboard", can("read dashboard")),
    group(
      "Estudiantes",
      "fa-solid fa-children",
      [
        sub("students.add", "Registro e inscripción", can("read student")),
        sub("students", "Listado y búsqueda", can("read student")),
        sub("students", "Expediente académico", can("read student")),
        sub("academic-groups", "Asignación a secciones", can("read academic groups")),
      ],
      can("read student")
    ),
    group(
      "Docentes",
      "fa-solid fa-chalkboard-user",
      [
        sub("teachers", "Registro de docentes", can("read teacher")),
        sub("teaching-assignments", "Asignación de materias", can("read teacher")),
        sub("teaching-assignments", "Carga académica", can("read teacher")),
      ],
      can("read teacher")
    ),
    group(
      "Académico",
      "fa-solid fa-graduation-cap",
      [
        sub("grades", "Grados y secciones", can("read grades")),
        sub("specialties", "Materias", can("read specialty")),
        sub("school-cycles", "Períodos escolares", can("read cycles")),
        sub("academic-groups", "Configuración académica", can("read academic groups")),
      ],
      can("read grades") || can("read specialty") || can("read cycles") || can("read academic groups")
    ),
    group(
      "Horarios",
      "fa-solid fa-clock",
      [
        sub("blank-page", "Crear horarios"),
        sub("blank-page", "Ver horarios"),
        sub("blank-page", "Validar conflictos"),
      ],
      true
    ),
    group(
      "Asistencia",
      "fa-solid fa-list-ol",
      [
        sub("assists", "Registrar asistencia", can("read assists")),
        sub("blank-page", "Justificaciones", can("read assists")),
        sub("reports.assists", "Reportes de asistencia", can("read assist")),
      ],
      can("read assists") || can("read assist")
    ),
    group(
      "Calificaciones",
      "fa-solid fa-chart-line",
      [
        sub("grades.qualification-record", "Registrar evaluaciones"),
        sub("blank-page", "Configurar ponderaciones"),
        sub("grades.qualification-record", "Consultar notas"),
      ],
      true
    ),
    group(
      "Comunicación",
      "fa-solid fa-comments",
      [
        sub("blank-page", "Mensajería"),
        sub("blank-page", "Avisos y circulares"),
        sub("blank-page", "Notificaciones"),
      ],
      true
    ),
    group(
      "Reportes",
      "fa-solid fa-file-lines",
      [
        sub("blank-page", "Boletines"),
        sub("reports.assists", "Estadísticas", can("read assist")),
        sub("reports.incidents", "Reportes administrativos", can("read incidents")),
      ],
      can("read assist") || can("read incidents")
    ),
    group(
      "Configuración",
      "fa-solid fa-gear",
      [
        sub("users", "Usuarios y roles", can("read user")),
        sub("general-config", "Parámetros del sistema", can("read general configuration")),
        sub("roles", "Roles y permisos", can("read role")),
      ],
      can("read user") || can("read general configuration") || can("read role")
    ),
  ].filter((n) => n.attributes.show);
}

/** Menu nodes for Docente */
export function menuTeacher(userStore) {
  const can = (p) => userStore.userCan(p);
  return [
    item("Dashboard", "fa fa-gauge", "dashboard", can("read dashboard")),
    group(
      "Mis Estudiantes",
      "fa-solid fa-children",
      [
        sub("blank-page", "Listado por sección"),
        sub("blank-page", "Consulta de expediente"),
      ],
      true
    ),
    item("Mi Horario", "fa-solid fa-clock", "blank-page", true),
    group(
      "Asistencia",
      "fa-solid fa-list-ol",
      [
        sub("assists", "Registrar asistencia", can("read assists")),
        sub("blank-page", "Ver justificaciones"),
      ],
      can("read assists")
    ),
    group(
      "Calificaciones",
      "fa-solid fa-chart-line",
      [
        sub("grades.qualification-record", "Registrar evaluaciones"),
        sub("grades.qualification-record", "Consultar notas"),
      ],
      true
    ),
    group(
      "Comunicación",
      "fa-solid fa-comments",
      [
        sub("blank-page", "Mensajes"),
        sub("blank-page", "Ver avisos"),
      ],
      true
    ),
    group(
      "Reportes",
      "fa-solid fa-file-lines",
      [
        sub("blank-page", "Listas de estudiantes"),
        sub("reports.assists", "Reportes de asistencia", can("read assist")),
      ],
      can("read assist")
    ),
  ].filter((n) => n.attributes.show);
}

/** Menu nodes for Estudiante */
export function menuStudent(userStore) {
  const can = (p) => userStore.userCan(p);
  return [
    item("Dashboard", "fa fa-gauge", "dashboard", can("read dashboard")),
    group(
      "Mi Información",
      "fa-solid fa-user",
      [
        sub("account-config", "Datos personales"),
        sub("blank-page", "Expediente académico"),
      ],
      true
    ),
    item("Mi Horario", "fa-solid fa-clock", "blank-page", true),
    item("Mi Asistencia", "fa-solid fa-list-ol", "assists", can("read assists")),
    group(
      "Mis Calificaciones",
      "fa-solid fa-chart-line",
      [
        sub("blank-page", "Ver calificaciones"),
        sub("blank-page", "Consultar promedios"),
      ],
      true
    ),
    group(
      "Comunicación",
      "fa-solid fa-comments",
      [
        sub("blank-page", "Mensajes"),
        sub("blank-page", "Ver avisos"),
      ],
      true
    ),
  ].filter((n) => n.attributes.show);
}

/** Menu nodes for Padre/Tutor */
export function menuParent(userStore) {
  const can = (p) => userStore.userCan(p);
  return [
    item("Dashboard", "fa fa-gauge", "dashboard", can("read dashboard")),
    group(
      "Mis Hijos",
      "fa-solid fa-people-roof",
      [
        sub("blank-page", "Seleccionar hijo"),
        sub("blank-page", "Datos personales"),
      ],
      true
    ),
    item("Horarios", "fa-solid fa-clock", "blank-page", true),
    group(
      "Asistencia",
      "fa-solid fa-list-ol",
      [
        sub("blank-page", "Consultar asistencia"),
        sub("blank-page", "Justificar ausencias"),
      ],
      true
    ),
    group(
      "Calificaciones",
      "fa-solid fa-chart-line",
      [
        sub("blank-page", "Ver calificaciones"),
        sub("blank-page", "Descargar boletines"),
      ],
      true
    ),
    group(
      "Comunicación",
      "fa-solid fa-comments",
      [
        sub("blank-page", "Mensajes con docentes"),
        sub("blank-page", "Ver avisos escolares"),
      ],
      true
    ),
  ].filter((n) => n.attributes.show);
}

export function getMenuNodesForRole(roleName, userStore) {
  const key = roleToMenuKey(roleName);
  switch (key) {
    case "teacher":
      return menuTeacher(userStore);
    case "student":
      return menuStudent(userStore);
    case "parent":
      return menuParent(userStore);
    default:
      return menuAdmin(userStore);
  }
}
