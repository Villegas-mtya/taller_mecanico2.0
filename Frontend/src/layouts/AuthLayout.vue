<template>
  <section class="auth-layout">
    <router-view />
  </section>
</template>

<style scoped>
.auth-layout {
  min-height: 100vh;
  display: grid;
  place-items: center;
  padding: 24px;
  background: var(--color-bg);
}
</style>