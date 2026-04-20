<script setup>
import { onMounted } from "vue";
import BaseButton from "@/components/base/BaseButton.vue";
import BaseCard from "@/components/base/BaseCard.vue";
import OrdenForm from "../components/OrdenForm.vue";
import OrdenesTable from "../components/OrdenesTable.vue";
import useOrdenes from "../composables/useOrdenes";

const {
  ordenes,
  clientes,
  vehiculos,
  cargando,
  guardando,
  editando,
  mostrarForm,
  busqueda,
  form,
  ordenesFiltradas,
  fetchInitialData,
  submitOrden,
  startEdit,
  removeOrden,
  resetForm,
} = useOrdenes();

onMounted(() => {
  fetchInitialData();
});

const nuevaOrden = () => {
  resetForm();
  mostrarForm.value = true;
};
</script>

<template>
  <section class="ordenes-view">
    <div class="page-header">
      <div>
        <p class="eyebrow">Módulo</p>
        <h1>Órdenes</h1>
        <p>Administra las órdenes de servicio del taller.</p>
      </div>

      <BaseButton type="button" @click="nuevaOrden">
        Nueva orden
      </BaseButton>
    </div>

    <BaseCard>
      <div class="toolbar">
        <div class="search-box">
          <label for="busqueda" class="sr-only">Buscar órdenes</label>
          <input
            id="busqueda"
            v-model="busqueda"
            type="text"
            class="search-input"
            placeholder="Buscar por cliente, vehículo, descripción o estado"
          />
        </div>
      </div>
    </BaseCard>

    <OrdenForm
      v-if="mostrarForm"
      v-model="form"
      :clientes="clientes"
      :vehiculos="vehiculos"
      :editando="editando"
      :guardando="guardando"
      @submit="submitOrden"
      @cancel="resetForm"
    />

    <OrdenesTable
      :ordenes="ordenesFiltradas"
      :cargando="cargando"
      @edit="startEdit"
      @delete="removeOrden"
      @clear-search="busqueda = ''"
    />
  </section>
</template>

<style scoped>
.ordenes-view {
  display: grid;
  gap: 20px;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 16px;
  flex-wrap: wrap;
}

.page-header h1 {
  margin: 0;
  color: #0f172a;
  font-size: 1.75rem;
}

.page-header p {
  margin: 6px 0 0;
  color: #64748b;
}

.eyebrow {
  margin: 0 0 8px;
  color: #6366f1;
  font-size: 13px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
}

.toolbar {
  padding: 20px;
}

.search-box {
  width: 100%;
  max-width: 420px;
}

.search-input {
  width: 100%;
  min-height: 44px;
  padding: 10px 14px;
  border: 1px solid #cbd5e1;
  border-radius: 10px;
  background: #fff;
  color: #0f172a;
}

.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border: 0;
}

@media (max-width: 768px) {
  .toolbar {
    padding: 16px;
  }

  .search-box {
    max-width: 100%;
  }
}
</style>