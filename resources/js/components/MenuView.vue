<template>
  <BaseNavigation :nodes="[
      {
      name: 'Dashboard',
        to: 'dashboard',
      icon: 'fa fa-gauge',
        attributes: {
        show: userStore.userCan('read dashboard'),
        },
    },
      //{
    //  name: 'Alumnos',
      //  icon: 'fa-solid fa-children',
    //  attributes: {
      //    show: true,
    //  },
      //  sub: [
    //    {
      //      name: 'Registrar',
    //      to: 'students.add',
      //      attributes: {
    //        show: userStore.userCan('read dinners'),
    //      },
    //    },
    //    {
    //      name: 'Lista de Alumnos',
    //      to: 'students',
    //      attributes: {
    //        show: userStore.userCan('read students'),
    //      },
    //    },
    //    {
    //      name: 'Comedor',
    //      to: 'dinners',
    //      attributes: {
    //        show: userStore.userCan('read dinners'),
    //      },
    //    },
    //    {
    //      name: 'Asistencia Comedor',
    //      to: 'assists-dinner',
    //      attributes: {
    //        show: userStore.userCan('read dinners'),
    //      },
    //    },
    //  ],
    //},
    {
      name: 'Asistencia',
      to: 'assists',
      icon: 'fa-solid fa-list-ol',
      attributes: {
        show: userStore.userCan('read assists'),
      },
    },
    {
      name: 'Comedor',
      icon: 'fa-solid fa-utensils',
      attributes: {
        show: true,
      },
      sub: [
        {
          name: 'Recargar Saldo',
          to: 'dinners',
          attributes: {
            show: userStore.userCan('read dinners'),
          },
        },
        {
          name: 'Asistencia Comedor',
          to: 'assists-dinner',
          attributes: {
            show: userStore.userCan('read dinners'),
          },
        },
      ],
    },
    {
      name: 'Datos Maestros',
      heading: true,
      attributes: {
        show:
          userStore.userCan('read student') &&
          userStore.userCan('read teacher') &&
          userStore.userCan('read specialty') &&
          userStore.userCan('read incident'),
      },
    },
    {
      name: 'Estudiantes',
      to: 'students',
      icon: 'fa-solid fa-children',
      attributes: {
        show: userStore.userCan('read student'),
      },
    },
    {
      name: 'Profesores',
      to: 'teachers',
      icon: 'fa-solid fa-chalkboard-user',
      attributes: {
        show: userStore.userCan('read teacher'),
      },
    },
    {
      name: 'Reportes',
      heading: true,
      attributes: {
        show: userStore.userCan('read assist') && userStore.userCan('read incidents'),
      },
    },
    {
      name: 'Asistencias',
      to: 'reports.assists',
      icon: 'fa-solid fa-location-dot',
      attributes: {
        show: userStore.userCan('read assist'),
      },
    },
    {
      name: 'Incidencias',
      to: 'reports.incidents',
      icon: 'fa-solid fa-location-dot',
      attributes: {
        show: userStore.userCan('read incidents'),
      },
    },
    {
      name: 'Configuración',
      heading: true,
      attributes: {
        show:
          userStore.userCan('read general configuration') &&
          userStore.userCan('read user') &&
          userStore.userCan('read role'),
      },
    },
    {
      name: 'General',
      to: 'general-config',
      icon: 'fa-solid fa-gear',
      attributes: {
        show: userStore.userCan('read general configuration'),
      },
    },
    {
      name: 'Académicos',
      icon: 'fa-solid fa-graduation-cap',
      attributes: {
        show: true,
      },
      sub: [
        {
          name: 'Grados',
          to: 'grades',
          attributes: {
            show: userStore.userCan('read grades'),
          },
        },
        {
          name: 'Grupos',
          to: 'sections',
          attributes: {
            show: userStore.userCan('read sections'),
          },
        },
        {
          name: 'Años',
          to: 'school-cycles',
          attributes: {
            show: userStore.userCan('read cycles'),
          },
        },
        {
          name: 'Grupos Académicos',
          to: 'academic-groups',
          attributes: {
            show: userStore.userCan('read academic groups'),
          },
        },
        {
          name: 'Especialidades',
          to: 'specialties',
          attributes: {
            show: userStore.userCan('read specialty'),
          },
        },
        {
          name: 'Incidentes',
          to: 'incidents',
          attributes: {
            show: userStore.userCan('read incident'),
          },
        },
      ],
    },


    {
      name: 'Usuarios',
      to: 'users',
      icon: 'fa-solid fa-users',
      attributes: {
        show: userStore.userCan('read user'),
      },
    },
    {
      name: 'Roles y Permisos',
      to: 'roles',
      icon: 'fa-solid fa-shield-halved',
      attributes: {
        show: userStore.userCan('read role'),
      },
    },
  ]" />
</template>

<script setup>
import BaseNavigation from "@/components/BaseNavigationTenant.vue";
import { useUserStore } from "@/stores/user";

const userStore = useUserStore();
</script>
