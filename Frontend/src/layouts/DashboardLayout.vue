<script setup>
import Sidebar from "@/components/layout/Sidebar.vue";
</script>

<template>
  <section class="dashboard-layout">
    <Sidebar />

    <main class="dashboard-main">
      <router-view />
    </main>
  </section>
</template>

<style scoped>
.dashboard-layout {
  min-height: 100vh;
  display: grid;
  grid-template-columns: 280px minmax(0, 1fr);
  background: var(--color-bg);
}

.dashboard-main {
  min-width: 0;
  padding: 24px;
}

@media (max-width: 900px) {
  .dashboard-layout {
    grid-template-columns: 1fr;
  }

  .dashboard-main {
    padding: 18px;
  }
}
</style>