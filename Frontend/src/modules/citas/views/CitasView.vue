<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useCitas } from '../composables/useCitas'

import BaseCard from '@/components/base/BaseCard.vue'
import BaseButton from '@/components/base/BaseButton.vue'
import CitasForm from '../components/CitasForm.vue'
import CitasTable from '../components/CitasTable.vue'

const router = useRouter()

const {
  citas,
  clientes,
  vehiculos,
  mecanicos,
  cargando,
  guardando,
  editando,
  mostrarForm,
  busqueda,
  form,
  citasFiltradas,
  generandoOrden,
  convertirCitaAOrden,
  fetchInitialData,
  submitCita,
  startEdit,
  removeCita,
  resetForm,
} = useCitas()

onMounted(fetchInitialData)

const toggleFormulario = () => {
  if (mostrarForm.value) {
    resetForm()
  } else {
    mostrarForm.value = true
  }
}

const verDetalle = (cita) => {
  router.push({
    name: 'citas-detalle',
    params: { id: cita.id },
  })
}

const generarOrdenDesdeCita = async (cita) => {
  try {
    await convertirCitaAOrden(cita.id)
    alert('Orden generada correctamente')
  } catch (error) {
    console.error('Error al generar orden:', error)
    alert(error?.response?.data?.message || 'No se pudo generar la orden')
  }
}
</script>

<template>
  <section class="page">
    <div class="page-shell">
      <header class="page-header">
        <div>
          <p class="eyebrow">Módulo</p>
          <h1>Gestión de citas</h1>
          <p class="subtitle">
            Administra citas, asignaciones y seguimiento previo a la orden de servicio.
          </p>
        </div>

        <BaseButton variant="primary" @click="toggleFormulario">
          {{ mostrarForm ? 'Cerrar formulario' : 'Nueva cita' }}
        </BaseButton>
      </header>

      <BaseCard>
        <div class="toolbar">
          <div class="toolbar-info">
            <span class="toolbar-label">Citas registradas</span>
            <strong class="toolbar-value">{{ citas.length }}</strong>
          </div>

          <div class="toolbar-search">
            <label for="buscarCita">Buscar</label>
            <input
              id="buscarCita"
              v-model="busqueda"
              type="text"
              class="search-input"
              placeholder="Buscar por cliente, vehículo, placas, servicio, mecánico o estado"
            />
          </div>
        </div>
      </BaseCard>

      <transition name="fade-slide">
        <CitasForm
          v-if="mostrarForm"
          v-model="form"
          :clientes="clientes"
          :vehiculos="vehiculos"
          :mecanicos="mecanicos"
          :editando="editando"
          :guardando="guardando"
          @submit="submitCita"
          @cancel="resetForm"
        />
      </transition>

      <CitasTable
        :citas="citasFiltradas"
        :cargando="cargando"
        :generando-orden="generandoOrden"
        @edit="startEdit"
        @delete="removeCita"
        @detail="verDetalle"
        @generar-orden="generarOrdenDesdeCita"
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
    radial-gradient(circle at top left, #e0f2fe 0, transparent 30%),
    radial-gradient(circle at bottom right, #ede9fe 0, transparent 30%),
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