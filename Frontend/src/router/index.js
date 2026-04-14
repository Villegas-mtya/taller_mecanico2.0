import { createRouter, createWebHistory } from "vue-router";

import DashboardLayout from "@/layouts/DashboardLayout.vue";
import AuthLayout from "@/layouts/AuthLayout.vue";

import DashboardView from "@/modules/dashboard/views/DashboardView.vue";
import ClientesView from "@/modules/clientes/views/ClientesView.vue";
import VehiculosView from "@/modules/vehiculos/views/VehiculosView.vue";
import OrdenesView from "@/modules/ordenes/views/OrdenesView.vue";
import ServiciosView from "@/modules/servicios/views/ServiciosView.vue";
import CitasView from "@/modules/citas/views/CitasView.vue";
import CitaDetalleView from "@/modules/citas/views/CitaDetalleView.vue";
import InventarioView from "@/modules/inventario/views/InventarioView.vue";
import LoginView from "@/modules/auth/views/LoginView.vue";

const routes = [
  {
    path: "/",
    component: DashboardLayout,
    children: [
      {
        path: "",
        name: "dashboard",
        component: DashboardView,
      },
      {
        path: "clientes",
        name: "clientes",
        component: ClientesView,
      },
      {
        path: "vehiculos",
        name: "vehiculos",
        component: VehiculosView,
      },
      {
        path: "ordenes",
        name: "ordenes",
        component: OrdenesView,
      },
      {
        path: "servicios",
        name: "servicios",
        component: ServiciosView,
      },
      {
        path: "citas",
        name: "citas",
        component: CitasView,
      },
      {
        path: "citas/:id",
        name: "citas-detalle",
        component: CitaDetalleView,
      },
      {
        path: "inventario",
        name: "inventario",
        component: InventarioView,
      },
    ],
  },
  {
    path: "/auth",
    component: AuthLayout,
    children: [
      {
        path: "login",
        name: "login",
        component: LoginView,
      },
    ],
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;