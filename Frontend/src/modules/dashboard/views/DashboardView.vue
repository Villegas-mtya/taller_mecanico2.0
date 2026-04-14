<script setup>
import { computed, onMounted, ref } from "vue";
import { useRouter } from "vue-router";

import Topbar from "@/components/layout/Topbar.vue";
import PageContainer from "@/components/layout/PageContainer.vue";

import { getClientes } from "@/modules/clientes/services/clientes.service";
import { getVehiculos } from "@/modules/vehiculos/services/vehiculos.service";
import { getOrdenes } from "@/modules/ordenes/services/ordenes.service";
import { get_citas } from "@/modules/citas/services/citas.service";

const router = useRouter();

const cargando = ref(false);

const totales = ref({
  clientes: 0,
  vehiculos: 0,
  ordenes: 0,
  citas: 0,
});

const cards = computed(() => [
  {
    title: "Clientes",
    value: totales.value.clientes,
    description: "Registros activos en la base de datos",
  },
  {
    title: "Vehículos",
    value: totales.value.vehiculos,
    description: "Vehículos asociados a clientes",
  },
  {
    title: "Órdenes",
    value: totales.value.ordenes,
    description: "Órdenes en proceso y finalizadas",
  },
  {
    title: "Citas",
    value: totales.value.citas,
    description: "Citas registradas en el sistema",
  },
]);

const accesosRapidos = [
  {
    label: "Crear cliente",
    description: "Abrir módulo de clientes",
    routeName: "clientes",
  },
  {
    label: "Registrar vehículo",
    description: "Abrir módulo de vehículos",
    routeName: "vehiculos",
  },
  {
    label: "Generar orden",
    description: "Abrir módulo de órdenes",
    routeName: "ordenes",
  },
  {
    label: "Ver citas",
    description: "Ir al panel de citas",
    routeName: "citas",
  },
];

const cargarResumen = async () => {
  cargando.value = true;

  try {
    const [clientes, vehiculos, ordenes, citas] = await Promise.all([
      getClientes(),
      getVehiculos(),
      getOrdenes(),
      get_citas(),
    ]);

    totales.value = {
      clientes: Array.isArray(clientes) ? clientes.length : 0,
      vehiculos: Array.isArray(vehiculos) ? vehiculos.length : 0,
      ordenes: Array.isArray(ordenes) ? ordenes.length : 0,
      citas: Array.isArray(citas) ? citas.length : 0,
    };
  } catch (error) {
    console.error("No se pudo cargar el resumen del dashboard:", error);
  } finally {
    cargando.value = false;
  }
};

const irA = (routeName) => {
  router.push({ name: routeName });
};

onMounted(cargarResumen);
</script>

<template>
  <PageContainer>
    <Topbar
      title="Dashboard"
      subtitle="Resumen general del sistema de administración del taller."
    />

    <section class="stats-grid">
      <article v-for="card in cards" :key="card.title" class="stat-card">
        <p class="stat-title">{{ card.title }}</p>
        <strong class="stat-value">{{ card.value }}</strong>
        <span class="stat-description">{{ card.description }}</span>
      </article>
    </section>

    <section class="dashboard-grid">
      <article class="dashboard-card">
        <h2>Actividad reciente</h2>
        <p>
          {{ cargando
            ? "Cargando indicadores del taller..."
            : "Resumen actualizado con la información real registrada en el sistema." }}
        </p>
      </article>

      <article class="dashboard-card">
        <h2>Accesos rápidos</h2>
        <div class="quick-actions">
          <button
            v-for="acceso in accesosRapidos"
            :key="acceso.routeName"
            type="button"
            class="quick-action"
            @click="irA(acceso.routeName)"
          >
            <strong>{{ acceso.label }}</strong>
            <span>{{ acceso.description }}</span>
          </button>
        </div>
      </article>
    </section>
  </PageContainer>
</template>

<style scoped>
.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
}

.stat-card,
.dashboard-card {
  padding: 24px;
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-md);
}

.stat-title {
  margin: 0;
  color: var(--color-text-muted);
  font-size: 0.95rem;
}

.stat-value {
  display: block;
  margin: 12px 0 8px;
  color: var(--color-text);
  font-size: 2rem;
  line-height: 1;
}

.stat-description {
  color: var(--color-text-soft);
  font-size: 0.9rem;
}

.dashboard-grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 20px;
}

.dashboard-card h2 {
  margin: 0 0 12px;
  color: var(--color-text);
}

.dashboard-card p {
  margin: 0;
  color: var(--color-text-muted);
}

.quick-actions {
  display: grid;
  gap: 10px;
}

.quick-action {
  text-align: left;
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  background: var(--color-bg);
  padding: 12px;
  cursor: pointer;
  display: grid;
  gap: 4px;
}

.quick-action strong {
  color: var(--color-text);
}

.quick-action span {
  color: var(--color-text-muted);
  font-size: 0.9rem;
}

.quick-action:hover {
  border-color: var(--color-primary);
}

@media (max-width: 1100px) {
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .dashboard-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 640px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }

  .stat-card,
  .dashboard-card {
    padding: 18px;
  }
}
</style>