<script setup>
defineProps({
  ordenes: {
    type: Array,
    default: () => [],
  },
  cargando: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['edit', 'delete', 'clear-search'])

const formatMoney = (value) => {
  const number = Number(value || 0)
  return new Intl.NumberFormat('es-MX', {
    style: 'currency',
    currency: 'MXN',
  }).format(number)
}
</script>

<template>
  <section class="card table-card">
    <div class="section-head">
      <div>
        <h2>Listado de órdenes</h2>
        <p>Consulta las órdenes registradas y su estado actual.</p>
      </div>

      <span class="results-badge">
        {{ ordenes.length }} resultado<span v-if="ordenes.length !== 1">s</span>
      </span>
    </div>

    <div v-if="cargando" class="empty-state">
      <h3>Cargando órdenes...</h3>
      <p>Espera un momento mientras se consulta la información.</p>
    </div>

    <div v-else-if="ordenes.length === 0" class="empty-state">
      <h3>Sin resultados</h3>
      <p>No encontramos órdenes con ese criterio de búsqueda.</p>
      <button type="button" class="btn btn-secondary" @click="emit('clear-search')">
        Limpiar búsqueda
      </button>
    </div>

    <div v-else class="table-wrapper">
      <table class="ordenes-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Vehículo</th>
            <th>Descripción</th>
            <th>Estado</th>
            <th>Total</th>
            <th>Creada</th>
            <th class="th-actions">Acciones</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="o in ordenes" :key="o.id">
            <td class="id-cell">{{ o.id }}</td>
            <td>
              {{
                o.clientenombre ||
                o.cliente_nombre ||
                o.nombre ||
                '-'
              }}
            </td>
            <td>
              {{
                o.vehiculonombre ||
                o.vehiculo_nombre ||
                o.vehiculo ||
                '-'
              }}
            </td>
            <td>{{ o.descripcion || '-' }}</td>
            <td>
              <span class="status-badge" :data-status="o.estado">
                {{ o.estado || '-' }}
              </span>
            </td>
            <td>{{ formatMoney(o.total) }}</td>
            <td>{{ o.createdat || o.created_at || '-' }}</td>
            <td class="actions-cell">
              <button type="button" class="btn btn-edit" @click="emit('edit', o)">
                Editar
              </button>
              <button type="button" class="btn btn-delete" @click="emit('delete', o.id)">
                Eliminar
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
</template>

<style scoped>
.card {
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-md);
}

.table-card {
  padding: 24px;
}

.section-head {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 16px;
  flex-wrap: wrap;
  margin-bottom: 20px;
}

.section-head h2 {
  margin: 0;
  color: var(--color-text);
  font-size: 1.35rem;
}

.section-head p {
  margin: 6px 0 0;
  color: var(--color-text-muted);
}

.results-badge {
  display: inline-flex;
  align-items: center;
  min-height: 40px;
  padding: 0 14px;
  border-radius: var(--radius-full);
  background: var(--color-surface-soft);
  color: var(--color-text-muted);
  border: 1px solid var(--color-border);
  font-weight: 600;
}

.table-wrapper {
  overflow-x: auto;
}

.ordenes-table {
  width: 100%;
  border-collapse: collapse;
}

.ordenes-table thead th {
  padding: 14px 12px;
  text-align: left;
  color: var(--color-text-muted);
  font-size: 0.85rem;
  font-weight: 700;
  border-bottom: 1px solid var(--color-border);
}

.ordenes-table tbody td {
  padding: 16px 12px;
  color: var(--color-text);
  border-bottom: 1px solid var(--color-divider);
  vertical-align: middle;
}

.ordenes-table tbody tr:hover {
  background: var(--color-row-hover);
}

.id-cell {
  color: var(--color-text-muted);
  font-weight: 600;
}

.status-badge {
  display: inline-flex;
  align-items: center;
  min-height: 32px;
  padding: 4px 10px;
  border-radius: var(--radius-full);
  border: 1px solid var(--color-border);
  background: var(--color-surface-soft);
  font-size: 0.85rem;
  font-weight: 700;
}

.status-badge[data-status='Pendiente'] {
  color: #9a6700;
  background: #fff8c5;
  border-color: #f2d675;
}

.status-badge[data-status='En proceso'] {
  color: #0958d9;
  background: #e6f4ff;
  border-color: #91caff;
}

.status-badge[data-status='Terminado'] {
  color: #237804;
  background: #f6ffed;
  border-color: #b7eb8f;
}

.status-badge[data-status='Entregado'] {
  color: #531dab;
  background: #f9f0ff;
  border-color: #d3adf7;
}

.th-actions,
.actions-cell {
  text-align: right;
  white-space: nowrap;
}

.actions-cell {
  display: flex;
  justify-content: flex-end;
  gap: 8px;
  flex-wrap: wrap;
}

.empty-state {
  padding: 50px 20px;
  text-align: center;
  color: var(--color-text-muted);
  display: grid;
  justify-items: center;
  gap: 10px;
}

.empty-state h3 {
  margin: 0;
  color: var(--color-text);
}

.btn {
  min-height: 44px;
  padding: 10px 16px;
  border: 1px solid transparent;
  border-radius: var(--radius-md);
  font-weight: 600;
  cursor: pointer;
}

.btn-secondary {
  background: var(--color-surface-soft);
  color: var(--color-text);
  border-color: var(--color-border);
}

.btn-edit {
  background: var(--color-primary-soft);
  color: var(--color-primary);
}

.btn-delete {
  background: var(--color-danger-soft);
  color: var(--color-danger);
}

@media (max-width: 768px) {
  .table-card {
    padding: 18px;
  }

  .th-actions,
  .actions-cell {
    text-align: left;
  }

  .actions-cell {
    justify-content: flex-start;
  }
}
</style>