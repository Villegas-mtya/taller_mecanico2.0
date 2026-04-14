<script setup>
import BaseCard from "@/components/base/BaseCard.vue";
import BaseButton from "@/components/base/BaseButton.vue";
import BaseTable from "@/components/base/BaseTable.vue";

defineProps({
  vehiculos: {
    type: Array,
    default: () => [],
  },
  cargando: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(["edit", "delete", "clear-search"]);
</script>

<template>
  <BaseCard>
    <div class="table-card">
      <div class="section-head">
        <div>
          <h2>Listado de vehículos</h2>
          <p>Consulta las unidades registradas y su cliente relacionado.</p>
        </div>

        <span class="results-badge">
          {{ vehiculos.length }} resultado<span v-if="vehiculos.length !== 1">s</span>
        </span>
      </div>

      <div v-if="cargando" class="empty-state">
        <h3>Cargando vehículos...</h3>
        <p>Espera un momento mientras se consulta la información.</p>
      </div>

      <div v-else-if="vehiculos.length === 0" class="empty-state">
        <h3>Sin resultados</h3>
        <p>No encontramos vehículos con ese criterio de búsqueda.</p>

        <BaseButton variant="secondary" @click="emit('clear-search')">
          Limpiar búsqueda
        </BaseButton>
      </div>

      <BaseTable v-else>
        <thead>
          <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Marca</th>
            <th>Modelo</th>
            <th>Año</th>
            <th>Placas</th>
            <th class="text-right">Acciones</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="v in vehiculos" :key="v.id">
            <td class="id-cell">#{{ v.id }}</td>
            <td>{{ v.cliente_nombre || "Sin cliente" }}</td>
            <td>{{ v.marca || "-" }}</td>
            <td>{{ v.modelo || "-" }}</td>
            <td>{{ v.anio || "-" }}</td>
            <td>{{ v.placas || "-" }}</td>
            <td class="actions-cell">
              <BaseButton variant="ghost" @click="emit('edit', v)">
                Editar
              </BaseButton>
              <BaseButton variant="danger" @click="emit('delete', v.id)">
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

@media (max-width: 768px) {
  .table-card {
    padding: 18px;
  }

  .actions-cell {
    justify-content: flex-start;
  }
}
</style>