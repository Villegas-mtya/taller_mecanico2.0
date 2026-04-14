<script setup>
import { onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { use_citas } from '../composables/useCitas'

import BaseButton from '@/components/base/BaseButton.vue'
import CitaDetalleCard from '../components/CitaDetalleCard.vue'
import PresupuestoItemsTable from '../components/PresupuestoItemsTable.vue'

const route = useRoute()
const router = useRouter()

const {
  detalle,
  presupuesto_items,
  orden_relacionada,
  total_presupuesto,
  cargando_detalle,
  generando_orden,
  fetch_detalle,
  generar_orden,
} = use_citas()

const cargar_detalle = async () => {
  const id = route.params.id
  if (!id) return
  await fetch_detalle(id)
}

const generar_orden_desde_vista = async () => {
  if (!detalle.value?.id) return

  try {
    await generar_orden(detalle.value.id)
  } catch (error) {
    console.error('No se pudo generar la orden:', error)
  }
}

const volver = () => {
  router.push({ name: 'citas' })
}

onMounted(cargar_detalle)

watch(
  () => route.params.id,
  async () => {
    await cargar_detalle()
  }
)
</script>

<template>
  <section class="page">
    <div class="page-shell">
      <header class="page-header">
        <div>
          <p class="eyebrow">Detalle de cita</p>
          <h1>Seguimiento de cita</h1>
          <p class="subtitle">
            Consulta la información consolidada de la cita y genera la orden cuando corresponda.
          </p>
        </div>

        <BaseButton variant="secondary" @click="volver">
          Volver
        </BaseButton>
      </header>

      <div v-if="cargando_detalle" class="empty-state">
        <h3>Cargando detalle...</h3>
        <p>Espera un momento mientras se consulta la información de la cita.</p>
      </div>

      <div v-else-if="!detalle" class="empty-state">
        <h3>No encontramos la cita</h3>
        <p>Verifica el identificador o vuelve al listado principal.</p>
        <BaseButton variant="secondary" @click="volver">
          Regresar
        </BaseButton>
      </div>

      <template v-else>
        <CitaDetalleCard
          :detalle="detalle"
          :total_presupuesto="total_presupuesto"
          :orden_relacionada="orden_relacionada"
          :generando_orden="generando_orden"
          @generar-orden="generar_orden_desde_vista"
        />

        <PresupuestoItemsTable
          :presupuesto_items="presupuesto_items"
          :total_presupuesto="total_presupuesto"
        />
      </template>
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

.empty-state {
  min-height: 260px;
  padding: 50px 20px;
  text-align: center;
  color: #64748b;
  display: grid;
  align-content: center;
  justify-items: center;
  gap: 10px;
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 20px;
}

.empty-state h3 {
  margin: 0;
  color: #0f172a;
}

@media (max-width: 768px) {
  .page-header h1 {
    font-size: 26px;
  }
}
</style>