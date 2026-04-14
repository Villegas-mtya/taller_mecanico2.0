<script setup>
import { RouterLink, useRoute } from "vue-router";

const route = useRoute();

const menu = [
  { name: "Dashboard", to: "/", icon: "📊", match: ["dashboard"] },
  { name: "Clientes", to: "/clientes", icon: "👥", match: ["clientes"] },
  { name: "Vehículos", to: "/vehiculos", icon: "🚗", match: ["vehiculos"] },
  { name: "Órdenes", to: "/ordenes", icon: "🧾", match: ["ordenes"] },
  { name: "Servicios", to: "/servicios", icon: "🛠️", match: ["servicios"] },
  { name: "Citas", to: "/citas", icon: "📅", match: ["citas", "citas-detalle"] },
  { name: "Inventario", to: "/inventario", icon: "📦", match: ["inventario"] },
];

const is_active = (item) => {
  return item.match.includes(route.name);
};
</script>

<template>
  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="brand-logo">T</div>

      <div>
        <strong>TallerControl</strong>
        <small>Panel administrativo</small>
      </div>
    </div>

    <nav class="sidebar-nav">
      <RouterLink
        v-for="item in menu"
        :key="item.to"
        :to="item.to"
        class="sidebar-link"
        :class="{ 'is-active': is_active(item) }"
      >
        <span class="sidebar-icon">{{ item.icon }}</span>
        <span>{{ item.name }}</span>
      </RouterLink>
    </nav>
  </aside>
</template>

<style scoped>
.sidebar {
  position: sticky;
  top: 0;
  height: 100vh;
  padding: 20px;
  border-right: 1px solid var(--color-border);
  background: var(--color-sidebar-bg);
  display: flex;
  flex-direction: column;
  gap: 24px;
  backdrop-filter: blur(10px);
}

.sidebar-brand {
  display: flex;
  align-items: center;
  gap: 12px;
}

.brand-logo {
  width: 44px;
  height: 44px;
  border-radius: 14px;
  display: grid;
  place-items: center;
  background: linear-gradient(135deg, var(--color-primary), var(--color-info));
  color: var(--color-white);
  font-weight: 800;
  box-shadow: var(--shadow-sm);
}

.sidebar-brand strong {
  display: block;
  color: var(--color-text);
}

.sidebar-brand small {
  display: block;
  margin-top: 2px;
  color: var(--color-text-muted);
}

.sidebar-nav {
  display: grid;
  gap: 8px;
}

.sidebar-link {
  min-height: 48px;
  padding: 12px 14px;
  border-radius: var(--radius-md);
  display: flex;
  align-items: center;
  gap: 12px;
  color: var(--color-text-muted);
  transition: all 0.2s ease;
}

.sidebar-link:hover {
  background: var(--color-surface-soft);
  color: var(--color-text);
}

.sidebar-link.router-link-active,
.sidebar-link.router-link-exact-active,
.sidebar-link.is-active {
  background: var(--color-primary-soft);
  color: var(--color-primary);
  font-weight: 700;
}

.sidebar-icon {
  width: 22px;
  text-align: center;
  flex-shrink: 0;
}

@media (max-width: 900px) {
  .sidebar {
    position: relative;
    height: auto;
    border-right: none;
    border-bottom: 1px solid var(--color-border);
  }
}
</style>