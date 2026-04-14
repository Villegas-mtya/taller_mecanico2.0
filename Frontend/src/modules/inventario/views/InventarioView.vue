<template>
  <section class="page">
    <div class="page-card">
      <p class="eyebrow">Inventario</p>
      <h1>Módulo de inventario</h1>
      <p>Aquí podrás gestionar refacciones, materiales y existencias disponibles.</p>
    </div>
  </section>
</template>

<style scoped>
.page {
  display: grid;
  gap: 24px;
}

.page-card {
  padding: 24px;
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-md);
}

.eyebrow {
  margin: 0 0 8px;
  color: var(--color-primary);
  font-size: 0.8rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

h1 {
  margin: 0 0 10px;
  color: var(--color-text);
}

p {
  margin: 0;
  color: var(--color-text-muted);
}
</style>