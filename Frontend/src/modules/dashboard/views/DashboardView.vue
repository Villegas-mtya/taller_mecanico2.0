<script setup>
import Topbar from "@/components/layout/Topbar.vue";
import PageContainer from "@/components/layout/PageContainer.vue";

const cards = [
  {
    title: "Clientes",
    value: "125",
    description: "Registros activos en la base de datos",
  },
  {
    title: "Vehículos",
    value: "89",
    description: "Vehículos asociados a clientes",
  },
  {
    title: "Órdenes",
    value: "34",
    description: "Órdenes en proceso y finalizadas",
  },
  {
    title: "Citas",
    value: "12",
    description: "Citas programadas para hoy",
  },
];
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
          Aquí podrás mostrar más adelante las últimas órdenes, clientes registrados
          y movimientos importantes del sistema.
        </p>
      </article>

      <article class="dashboard-card">
        <h2>Accesos rápidos</h2>
        <ul class="quick-list">
          <li>Crear cliente</li>
          <li>Registrar vehículo</li>
          <li>Generar orden</li>
          <li>Ver citas del día</li>
        </ul>
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

.quick-list {
  margin: 0;
  padding-left: 18px;
  color: var(--color-text-muted);
  display: grid;
  gap: 10px;
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