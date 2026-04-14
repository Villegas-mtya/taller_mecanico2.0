<template>
  <div class="base-table-wrapper">
    <table class="base-table">
      <slot />
    </table>
  </div>
</template>

<style scoped>
.base-table-wrapper {
  overflow-x: auto;
}

.base-table {
  width: 100%;
  border-collapse: collapse;
}

.base-table :deep(thead th) {
  text-align: left;
  font-size: 13px;
  color: #64748b;
  font-weight: 700;
  padding: 14px 12px;
  border-bottom: 1px solid #e2e8f0;
}

.base-table :deep(tbody td) {
  padding: 16px 12px;
  border-bottom: 1px solid #eef2f7;
  color: #334155;
  vertical-align: middle;
}

.base-table :deep(tbody tr:hover) {
  background: #f8fafc;
}
</style>