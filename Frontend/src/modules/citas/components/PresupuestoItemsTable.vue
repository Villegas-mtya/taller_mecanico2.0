<script setup>
import BaseCard from '@/components/base/BaseCard.vue'
import BaseTable from '@/components/base/BaseTable.vue'

const props = defineProps({
  presupuesto_items: {
    type: Array,
    default: () => [],
  },
  total_presupuesto: {
    type: Number,
    default: 0,
  },
})

const formato_moneda = (valor) => {
  return Number(valor || 0).toLocaleString('es-MX', {
    style: 'currency',
    currency: 'MXN',
  })
}
</script>

<template>
  <BaseCard>
    <div class="table-card">
      <div class="section-head">
        <div>
          <h2>Partidas de presupuesto</h2>
          <p>Listado de conceptos ligados a la cita seleccionada.</p>
        </div>

        <span class="results-badge">
          {{ presupuesto_items.length }} partida<span v-if="presupuesto_items.length !== 1">s</span>
        </span>
      </div>

      <div v-if="presupuesto_items.length === 0" class="empty-state">
        <h3>Sin partidas</h3>
        <p>Esta cita todavía no tiene elementos registrados en presupuestoitems.</p>
      </div>

      <BaseTable v-else>
        <thead>
          <tr>
            <th>ID</th>
            <th>Tipo</th>
            <th>Descripción</th>
            <th>Cantidad</th>
            <th>Precio unitario</th>
            <th>Importe</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="item in presupuesto_items" :key="item.id">
            <td class="id-cell">{{ item.id }}</td>
            <td>{{ item.tipoitem || '-' }}</td>
            <td>{{ item.descripcion || '-' }}</td>
            <td>{{ item.cantidad || 0 }}</td>
            <td>{{ formato_moneda(item.preciounitario) }}</td>
            <td class="importe-cell">{{ formato_moneda(item.importe) }}</td>
          </tr>
        </tbody>
      </BaseTable>

      <div class="total-box">
        <span>Total del presupuesto</span>
        <strong>{{ formato_moneda(total_presupuesto) }}</strong>
      </div>
    </div>
  </BaseCard>
</template>

<style scoped>
.table-card {
  padding: 24px;
  display: grid;
  gap: 20px;
}

.section-head {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 16px;
  flex-wrap: wrap;
}

.section-head h2 {
  margin: 0;
  color: #0f172a;
  font-size: 1.35rem;
}

.section-head p {
  margin: 6px 0 0;
  color: #64748b;
}

.results-badge {
  display: inline-flex;
  align-items: center;
  min-height: 40px;
  padding: 0 14px;
  border-radius: 999px;
  background: #eef2ff;
  color: #475569;
  border: 1px solid #dbeafe;
  font-weight: 600;
}

.empty-state {
  padding: 50px 20px;
  text-align: center;
  color: #64748b;
  display: grid;
  justify-items: center;
  gap: 10px;
}

.empty-state h3 {
  margin: 0;
  color: #0f172a;
}

.id-cell {
  color: #475569;
  font-weight: 700;
}

.importe-cell {
  font-weight: 700;
  color: #0f172a;
}

.total-box {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 16px;
  padding: 16px 18px;
  border-radius: 16px;
  background: #0f172a;
  color: #fff;
}

.total-box span {
  opacity: 0.85;
}

.total-box strong {
  font-size: 1.1rem;
}

@media (max-width: 768px) {
  .table-card {
    padding: 18px;
  }

  .total-box {
    flex-direction: column;
    align-items: flex-start;
  }
}
</style>