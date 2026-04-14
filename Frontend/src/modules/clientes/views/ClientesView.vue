<script setup>
import { onMounted } from "vue";
import { useClientes } from "../composables/useClientes";

import BaseCard from "@/components/base/BaseCard.vue";
import BaseButton from "@/components/base/BaseButton.vue";

import ClientesForm from "../components/ClientesForm.vue";
import ClientesTable from "../components/ClientesTable.vue";

const {
  clientes,
  cargando,
  guardando,
  editando,
  mostrarForm,
  busqueda,
  form,
  clientesFiltrados,
  obtenerClientes,
  limpiarForm,
  eliminarCliente,
  editarCliente,
  submitCliente,
} = useClientes();

onMounted(obtenerClientes);
</script>

<template>
  <section class="page">
    <div class="page-shell">
      <header class="page-header">
        <div>
          <p class="eyebrow">Módulo</p>
          <h1>Gestión de clientes</h1>
          <p class="subtitle">
            Administra los clientes registrados y mantén su información actualizada.
          </p>
        </div>

        <BaseButton
          variant="primary"
          @click="mostrarForm = !mostrarForm"
        >
          {{ mostrarForm ? "Cerrar formulario" : "Nuevo cliente" }}
        </BaseButton>
      </header>

      <BaseCard>
        <div class="toolbar">
          <div class="toolbar-info">
            <span class="toolbar-label">Clientes registrados</span>
            <strong class="toolbar-value">{{ clientesFiltrados.length }}</strong>
          </div>

          <div class="toolbar-search">
            <label for="buscarCliente">Buscar</label>
            <input
              id="buscarCliente"
              v-model="busqueda"
              type="text"
              class="search-input"
              placeholder="Buscar por nombre, teléfono o correo"
            />
          </div>
        </div>
      </BaseCard>

      <transition name="fade-slide">
        <ClientesForm
          v-if="mostrarForm"
          v-model="form"
          :editando="editando"
          :guardando="guardando"
          @submit="submitCliente"
          @cancel="limpiarForm"
        />
      </transition>

      <ClientesTable
        :clientes="clientesFiltrados"
        :cargando="cargando"
        @edit="editarCliente"
        @delete="eliminarCliente"
        @clear-search="busqueda = ''"
      />
    </div>
  </section>
</template>

<style scoped>
.page {
  min-height: 100vh;
  padding: 32px 20px;
  background:
    radial-gradient(circle at top left, #e0f2fe 0%, transparent 30%),
    radial-gradient(circle at bottom right, #ede9fe 0%, transparent 30%),
    #f8fafc;
}

.page-shell {
  max-width: 1200px;
  margin: 0 auto;
  display: grid;
  gap: 24px;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 20px;
  flex-wrap: wrap;
}

.eyebrow {
  margin: 0 0 8px;
  color: #6366f1;
  font-size: 13px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
}

.page-header h1 {
  margin: 0;
  color: #0f172a;
  font-size: 32px;
  line-height: 1.1;
}

.subtitle {
  margin: 8px 0 0;
  color: #64748b;
  font-size: 15px;
}

.toolbar {
  padding: 20px;
  display: flex;
  justify-content: space-between;
  gap: 20px;
  align-items: center;
  flex-wrap: wrap;
}

.toolbar-info {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.toolbar-label {
  color: #64748b;
  font-size: 14px;
}

.toolbar-value {
  font-size: 28px;
  color: #0f172a;
}

.toolbar-search {
  flex: 1;
  min-width: 280px;
  display: grid;
  gap: 8px;
}

.toolbar-search label {
  color: #334155;
  font-weight: 600;
  font-size: 14px;
}

.search-input {
  width: 100%;
  min-height: 48px;
  padding: 12px 14px;
  background: #fff;
  color: #0f172a;
  border: 1px solid #cbd5e1;
  border-radius: 12px;
  outline: none;
  transition: 0.2s ease;
}

.search-input:focus {
  border-color: #6366f1;
  box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.12);
}

.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.25s ease;
}

.fade-slide-enter-from,
.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}

@media (max-width: 768px) {
  .page-header h1 {
    font-size: 26px;
  }

  .toolbar {
    padding: 18px;
  }
}
</style>