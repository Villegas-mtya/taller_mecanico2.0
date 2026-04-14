<script setup>
import BaseCard from '@/components/base/BaseCard.vue'
import BaseButton from '@/components/base/BaseButton.vue'
import BaseTable from '@/components/base/BaseTable.vue'

const props = defineProps({
  citas: {
    type: Array,
    default: () => [],
  },
  cargando: {
    type: Boolean,
    default: false,
  },
  generandoOrden: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['edit', 'delete', 'generar-orden', 'clear-search'])

const claseEstado = (estado) => {
  switch (estado) {
    case 'Pendiente':
      return 'badge badge-pendiente'
    case 'Confirmada':
      return 'badge badge-confirmada'
    case 'En proceso':
      return 'badge badge-proceso'
    case 'Convertida en orden':
      return 'badge badge-convertida'
    case 'Cancelada':
      return 'badge badge-cancelada'
    default:
      return 'badge'
  }
}

const puedeGenerarOrden = (cita) => {
  return cita.estado !== 'Convertida en orden'
}
</script>

<template>
  <BaseCard>
    <div class="table-card">
      <div class="section-head">
        <div>
          <h2>Listado de citas</h2>
          <p>Consulta, edita y convierte citas en órdenes de servicio.</p>
        </div>

        <span class="results-badge">
          {{ citas.length }} resultado<span v-if="citas.length !== 1">s</span>
        </span>
      </div>

      <div v-if="cargando" class="empty-state">
        <h3>Cargando citas...</h3>
        <p>Espera un momento mientras se consulta la información.</p>
      </div>

      <div v-else-if="citas.length === 0" class="empty-state">
        <h3>Sin resultados</h3>
        <p>No encontramos citas con ese criterio de búsqueda.</p>
        <BaseButton variant="secondary" @click="emit('clear-search')">
          Limpiar búsqueda
        </BaseButton>
      </div>

      <BaseTable v-else>
        <thead>
          <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Vehículo</th>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Estado</th>
            <th>Servicio</th>
            <th class="text-right">Acciones</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="cita in citas" :key="cita.id">
            <td class="id-cell">{{ cita.id }}</td>
            <td>{{ cita.clientenombre || cita.cliente_nombre || '-' }}</td>
            <td>
              {{ [cita.marca, cita.modelo, cita.placas].filter(Boolean).join(' ') || '-' }}
            </td>
            <td>{{ cita.fecha || '-' }}</td>
            <td>{{ cita.hora || '-' }}</td>
            <td>
              <span :class="claseEstado(cita.estado)">
                {{ cita.estado || '-' }}
              </span>
            </td>
            <td>{{ cita.tipo_servicio || cita.tiposervicio || '-' }}</td>
            <td class="actions-cell">
              <BaseButton variant="ghost" @click="emit('edit', cita)">
                Editar
              </BaseButton>

              <BaseButton
                variant="success"
                :disabled="generandoOrden || !puedeGenerarOrden(cita)"
                @click="emit('generar-orden', cita)"
              >
                {{
                  !puedeGenerarOrden(cita)
                    ? 'Ya convertida'
                    : generandoOrden
                    ? 'Generando...'
                    : 'Generar orden'
                }}
              </BaseButton>

              <BaseButton variant="danger" @click="emit('delete', cita.id)">
                Eliminar
              </BaseButton>
            </td>
          </tr>
        </tbody>
      </BaseTable>
    </div>
  </BaseCard>
</template>

<style scoped>
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

.id-cell {
  color: #475569;
  font-weight: 700;
}

.actions-cell {
  display: flex;
  justify-content: flex-end;
  gap: 8px;
  flex-wrap: wrap;
}

.text-right {
  text-align: right;
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

.badge {
  display: inline-flex;
  align-items: center;
  min-height: 32px;
  padding: 0 10px;
  border-radius: 999px;
  font-size: 13px;
  font-weight: 700;
  border: 1px solid transparent;
}

.badge-pendiente {
  background: #fff7ed;
  color: #9a3412;
  border-color: #fdba74;
}

.badge-confirmada {
  background: #eff6ff;
  color: #1d4ed8;
  border-color: #93c5fd;
}

.badge-proceso {
  background: #fefce8;
  color: #a16207;
  border-color: #fde047;
}

.badge-convertida {
  background: #ecfdf5;
  color: #047857;
  border-color: #86efac;
}

.badge-cancelada {
  background: #fef2f2;
  color: #b91c1c;
  border-color: #fca5a5;
}

@media (max-width: 768px) {
  .table-card {
    padding: 18px;
  }

  .actions-cell {
    justify-content: flex-start;
  }
}
</style>