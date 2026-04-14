<script setup>
defineProps({
  title: {
    type: String,
    default: "Dashboard",
  },
  subtitle: {
    type: String,
    default: "",
  },
});
</script>

<template>
  <header class="topbar">
    <div>
      <p class="topbar-eyebrow">TallerControl 2.0</p>
      <h1 class="topbar-title">{{ title }}</h1>
      <p v-if="subtitle" class="topbar-subtitle">{{ subtitle }}</p>
    </div>

    <div class="topbar-actions">
      <button class="topbar-btn" type="button">🔔</button>
      <button class="topbar-btn" type="button">👤</button>
    </div>
  </header>
</template>

<style scoped>
.topbar {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 20px;
  flex-wrap: wrap;
  padding: 20px 24px;
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-sm);
}

.topbar-eyebrow {
  margin: 0 0 6px;
  color: var(--color-primary);
  font-size: 0.8rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
}

.topbar-title {
  margin: 0;
  color: var(--color-text);
  font-size: 1.8rem;
  line-height: 1.1;
}

.topbar-subtitle {
  margin: 8px 0 0;
  color: var(--color-text-muted);
}

.topbar-actions {
  display: flex;
  gap: 10px;
}

.topbar-btn {
  width: 44px;
  height: 44px;
  border: 1px solid var(--color-border);
  border-radius: 50%;
  background: var(--color-surface-soft);
  color: var(--color-text);
}

@media (max-width: 768px) {
  .topbar {
    padding: 18px;
  }

  .topbar-title {
    font-size: 1.45rem;
  }
}
</style>