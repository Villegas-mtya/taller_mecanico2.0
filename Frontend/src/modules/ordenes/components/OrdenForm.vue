<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: Object,
    required: true,
  },
  clientes: {
    type: Array,
    default: () => [],
  },
  vehiculos: {
    type: Array,
    default: () => [],
  },
  inventario: {
    type: Array,
    default: () => [],
  },
  servicios: {
    type: Array,
    default: () => [],
  },
  editando: {
    type: Boolean,
    default: false,
  },
  guardando: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['update:modelValue', 'submit', 'cancel'])

const moneyFormatter = new Intl.NumberFormat('es-MX', {
  style: 'currency',
  currency: 'MXN',
})

const formatMoney = (value) => moneyFormatter.format(Number(value || 0))

const toNumber = (value) => {
  const number = Number(value)
  return Number.isFinite(number) && number > 0 ? number : 0
}

const roundCurrency = (value) => Math.round((Number(value || 0) + Number.EPSILON) * 100) / 100

const getSelection = (field) => (Array.isArray(props.modelValue[field]) ? props.modelValue[field] : [])

const getSelectedItem = (field, id) => getSelection(field).find((item) => String(item.id) === String(id))

const getSelectedQuantity = (field, id) => getSelectedItem(field, id)?.cantidad ?? 1

const hasSelectedItems = computed(() =>
  getSelection('inventarioItems').length > 0 || getSelection('serviciosItems').length > 0
)

const selectedInventorySubtotal = computed(() =>
  getSelection('inventarioItems').reduce((total, item) => total + toNumber(item.cantidad) * toNumber(item.precio), 0)
)

const selectedServicesSubtotal = computed(() =>
  getSelection('serviciosItems').reduce((total, item) => total + toNumber(item.cantidad) * toNumber(item.precio), 0)
)

const calculatedTotal = computed(() => roundCurrency(selectedInventorySubtotal.value + selectedServicesSubtotal.value))

const displayedTotal = computed(() => (
  hasSelectedItems.value || props.modelValue.cotizacionActiva
    ? calculatedTotal.value
    : roundCurrency(props.modelValue.total)
))

const activeInventory = computed(() =>
  props.inventario.filter((item) => Number(item.activo ?? 1) === 1)
)

const activeServices = computed(() =>
  props.servicios.filter((servicio) => Number(servicio.activo ?? 1) === 1)
)

const buildInventorySelection = (item, cantidad = 1) => ({
  id: item.id,
  nombre: item.nombre || 'Item de inventario',
  cantidad: Math.max(1, Number(cantidad || 1)),
  precio: roundCurrency(item.costo),
  tipo: 'inventario',
})

const buildServiceSelection = (servicio, cantidad = 1) => ({
  id: servicio.id,
  nombre: servicio.nombre || 'Servicio',
  cantidad: Math.max(1, Number(cantidad || 1)),
  precio: roundCurrency(servicio.precio),
  tipo: 'servicio',
})

// Algoritmo de cotización: suma cantidad * precio de inventario y servicios seleccionados.
const calculateTotalForPayload = (inventarioItems, serviciosItems) => {
  const inventoryTotal = inventarioItems.reduce((total, item) => total + toNumber(item.cantidad) * toNumber(item.precio), 0)
  const servicesTotal = serviciosItems.reduce((total, item) => total + toNumber(item.cantidad) * toNumber(item.precio), 0)
  return roundCurrency(inventoryTotal + servicesTotal)
}

// Cada cambio emite el formulario completo para mantener sincronizado el v-model del padre.
const emitUpdatedForm = (changes) => {
  const nextForm = {
    ...props.modelValue,
    ...changes,
  }

  const nextInventory = Array.isArray(nextForm.inventarioItems) ? nextForm.inventarioItems : []
  const nextServices = Array.isArray(nextForm.serviciosItems) ? nextForm.serviciosItems : []
  const hasSelections = nextInventory.length > 0 || nextServices.length > 0

  emit('update:modelValue', {
    ...nextForm,
    total: hasSelections || nextForm.cotizacionActiva
      ? calculateTotalForPayload(nextInventory, nextServices)
      : roundCurrency(nextForm.total),
  })
}

const updateField = (field, value) => {
  emitUpdatedForm({ [field]: value })
}

const toggleInventoryItem = (item, checked) => {
  const currentItems = getSelection('inventarioItems')
  const nextItems = checked
    ? [...currentItems, buildInventorySelection(item)]
    : currentItems.filter((selectedItem) => String(selectedItem.id) !== String(item.id))

  emitUpdatedForm({ inventarioItems: nextItems, cotizacionActiva: true })
}

const toggleService = (servicio, checked) => {
  const currentServices = getSelection('serviciosItems')
  const nextServices = checked
    ? [...currentServices, buildServiceSelection(servicio)]
    : currentServices.filter((selectedService) => String(selectedService.id) !== String(servicio.id))

  emitUpdatedForm({ serviciosItems: nextServices, cotizacionActiva: true })
}

const updateInventoryQuantity = (item, value) => {
  const stockDisponible = Math.max(1, Number(item.stock || 0))
  const quantity = Math.min(stockDisponible, Math.max(1, Number(value || 1)))
  const nextItems = getSelection('inventarioItems').map((selectedItem) => (
    String(selectedItem.id) === String(item.id)
      ? buildInventorySelection(item, quantity)
      : selectedItem
  ))

  emitUpdatedForm({ inventarioItems: nextItems, cotizacionActiva: true })
}

const updateServiceQuantity = (servicio, value) => {
  const quantity = Math.max(1, Number(value || 1))
  const nextServices = getSelection('serviciosItems').map((selectedService) => (
    String(selectedService.id) === String(servicio.id)
      ? buildServiceSelection(servicio, quantity)
      : selectedService
  ))

  emitUpdatedForm({ serviciosItems: nextServices, cotizacionActiva: true })
}
</script>

<template>
  <section class="card form-card">
    <div class="section-head">
      <div>
        <h2>{{ editando ? 'Editar orden' : 'Nueva orden' }}</h2>
        <p>Registra una orden y selecciona inventario o servicios para calcular la cotización automáticamente.</p>
      </div>
    </div>

    <form class="orden-form" @submit.prevent="emit('submit')">
      <div class="form-grid">
        <div class="field">
          <label for="cliente_id">Cliente</label>
          <select
            id="cliente_id"
            class="input"
            :value="modelValue.cliente_id"
            @change="updateField('cliente_id', $event.target.value)"
            required
          >
            <option value="">Selecciona un cliente</option>
            <option
              v-for="cliente in clientes"
              :key="cliente.id"
              :value="cliente.id"
            >
              {{ cliente.nombre }}
            </option>
          </select>
        </div>

        <div class="field">
          <label for="vehiculo_id">Vehículo</label>
          <select
            id="vehiculo_id"
            class="input"
            :value="modelValue.vehiculo_id"
            @change="updateField('vehiculo_id', $event.target.value)"
            required
          >
            <option value="">Selecciona un vehículo</option>
            <option
              v-for="vehiculo in vehiculos"
              :key="vehiculo.id"
              :value="vehiculo.id"
            >
              {{ vehiculo.marca }} {{ vehiculo.modelo }} - {{ vehiculo.placas || vehiculo.placa || '-' }}
            </option>
          </select>
        </div>
      </div>

      <div class="field">
        <label for="descripcion">Descripción del servicio</label>
        <textarea
          id="descripcion"
          class="input textarea"
          :value="modelValue.descripcion"
          @input="updateField('descripcion', $event.target.value)"
          placeholder="Describe el problema o trabajo a realizar"
          required
        />
      </div>

      <div class="quote-grid">
        <section class="quote-section">
          <div class="quote-section__header">
            <h3>Inventario</h3>
            <p>Selecciona refacciones o materiales disponibles.</p>
          </div>

          <div v-if="activeInventory.length === 0" class="quote-empty">
            No hay items activos en inventario.
          </div>

          <div v-else class="quote-list">
            <article
              v-for="item in activeInventory"
              :key="item.id"
              class="quote-item"
              :class="{ 'quote-item--disabled': Number(item.stock || 0) <= 0 }"
            >
              <label class="quote-item__main">
                <input
                  type="checkbox"
                  :checked="Boolean(getSelectedItem('inventarioItems', item.id))"
                  :disabled="Number(item.stock || 0) <= 0"
                  @change="toggleInventoryItem(item, $event.target.checked)"
                />
                <span>
                  <strong>{{ item.nombre || 'Sin nombre' }}</strong>
                  <small>Stock: {{ Number(item.stock || 0) }} · {{ formatMoney(item.costo) }}</small>
                </span>
              </label>

              <div
                v-if="getSelectedItem('inventarioItems', item.id)"
                class="quantity-control"
              >
                <label :for="`inventario-cantidad-${item.id}`">Cantidad</label>
                <input
                  :id="`inventario-cantidad-${item.id}`"
                  type="number"
                  min="1"
                  :max="Math.max(1, Number(item.stock || 0))"
                  step="1"
                  class="input quantity-input"
                  :value="getSelectedQuantity('inventarioItems', item.id)"
                  @input="updateInventoryQuantity(item, $event.target.value)"
                />
              </div>
            </article>
          </div>
        </section>

        <section class="quote-section">
          <div class="quote-section__header">
            <h3>Servicios</h3>
            <p>Agrega uno o más servicios a la cotización.</p>
          </div>

          <div v-if="activeServices.length === 0" class="quote-empty">
            No hay servicios activos registrados.
          </div>

          <div v-else class="quote-list">
            <article
              v-for="servicio in activeServices"
              :key="servicio.id"
              class="quote-item"
            >
              <label class="quote-item__main">
                <input
                  type="checkbox"
                  :checked="Boolean(getSelectedItem('serviciosItems', servicio.id))"
                  @change="toggleService(servicio, $event.target.checked)"
                />
                <span>
                  <strong>{{ servicio.nombre || 'Sin nombre' }}</strong>
                  <small>{{ formatMoney(servicio.precio) }}</small>
                </span>
              </label>

              <div
                v-if="getSelectedItem('serviciosItems', servicio.id)"
                class="quantity-control"
              >
                <label :for="`servicio-cantidad-${servicio.id}`">Cantidad</label>
                <input
                  :id="`servicio-cantidad-${servicio.id}`"
                  type="number"
                  min="1"
                  step="1"
                  class="input quantity-input"
                  :value="getSelectedQuantity('serviciosItems', servicio.id)"
                  @input="updateServiceQuantity(servicio, $event.target.value)"
                />
              </div>
            </article>
          </div>
        </section>
      </div>

      <section class="quote-summary" aria-live="polite">
        <div>
          <span>Subtotal inventario</span>
          <strong>{{ formatMoney(selectedInventorySubtotal) }}</strong>
        </div>
        <div>
          <span>Subtotal servicios</span>
          <strong>{{ formatMoney(selectedServicesSubtotal) }}</strong>
        </div>
        <div class="quote-summary__total">
          <span>Total cotizado</span>
          <strong>{{ formatMoney(displayedTotal) }}</strong>
        </div>
      </section>

      <div class="form-grid">
        <div class="field">
          <label for="estado">Estado</label>
          <select
            id="estado"
            class="input"
            :value="modelValue.estado"
            @change="updateField('estado', $event.target.value)"
          >
            <option value="Pendiente">Pendiente</option>
            <option value="En Proceso">En Proceso</option>
            <option value="Terminado">Terminado</option>
          </select>
        </div>

        <div class="field">
          <label for="total">Total automático</label>
          <input
            id="total"
            class="input total-input"
            type="number"
            min="0"
            step="0.01"
            :value="displayedTotal"
            placeholder="0.00"
            readonly
          />
          <small class="field-help">El total se actualiza al seleccionar inventario o servicios.</small>
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-success" :disabled="guardando">
          {{ guardando ? 'Guardando...' : editando ? 'Actualizar orden' : 'Guardar orden' }}
        </button>

        <button type="button" class="btn btn-secondary" @click="emit('cancel')">
          Cancelar
        </button>
      </div>
    </form>
  </section>
</template>

<style scoped>
.card {
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-md);
}

.form-card {
  padding: 24px;
}

.section-head {
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

.orden-form {
  display: grid;
  gap: 18px;
}

.form-grid,
.quote-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
}

.field {
  display: grid;
  gap: 8px;
}

.field label {
  color: var(--color-text);
  font-weight: 600;
  font-size: 0.95rem;
}

.field-help {
  color: var(--color-text-muted);
}

.input {
  width: 100%;
  min-height: 48px;
  padding: 12px 14px;
  background: var(--color-input-bg);
  color: var(--color-text);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  outline: none;
}

.input:focus {
  border-color: var(--color-primary);
  box-shadow: 0 0 0 4px var(--color-focus-ring);
}

.textarea {
  min-height: 120px;
  resize: vertical;
}

.total-input {
  background: var(--color-surface-soft);
  font-weight: 700;
}

.quote-section {
  display: grid;
  gap: 14px;
  padding: 18px;
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg);
  background: var(--color-surface-soft);
}

.quote-section__header h3 {
  margin: 0;
  color: var(--color-text);
  font-size: 1.05rem;
}

.quote-section__header p,
.quote-empty {
  margin: 4px 0 0;
  color: var(--color-text-muted);
  font-size: 0.92rem;
}

.quote-list {
  display: grid;
  gap: 10px;
  max-height: 340px;
  overflow-y: auto;
  padding-right: 4px;
}

.quote-item {
  display: grid;
  gap: 12px;
  padding: 14px;
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  background: var(--color-surface);
}

.quote-item--disabled {
  opacity: 0.6;
}

.quote-item__main {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  cursor: pointer;
}

.quote-item__main input {
  width: 18px;
  height: 18px;
  margin-top: 2px;
}

.quote-item__main span {
  display: grid;
  gap: 3px;
}

.quote-item__main strong {
  color: var(--color-text);
}

.quote-item__main small {
  color: var(--color-text-muted);
}

.quantity-control {
  display: grid;
  grid-template-columns: 90px 120px;
  align-items: center;
  gap: 10px;
}

.quantity-control label {
  color: var(--color-text-muted);
  font-size: 0.88rem;
  font-weight: 600;
}

.quantity-input {
  min-height: 40px;
  padding: 8px 10px;
}

.quote-summary {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
  padding: 16px;
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg);
  background: var(--color-surface);
}

.quote-summary div {
  display: grid;
  gap: 4px;
}

.quote-summary span {
  color: var(--color-text-muted);
  font-size: 0.9rem;
}

.quote-summary strong {
  color: var(--color-text);
  font-size: 1.05rem;
}

.quote-summary__total strong {
  color: var(--color-primary);
  font-size: 1.25rem;
}

.form-actions {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
}

.btn {
  min-height: 44px;
  padding: 10px 16px;
  border: 1px solid transparent;
  border-radius: var(--radius-md);
  font-weight: 600;
  cursor: pointer;
}

.btn-success {
  background: var(--color-success);
  color: var(--color-white);
}

.btn-secondary {
  background: var(--color-surface-soft);
  color: var(--color-text);
  border-color: var(--color-border);
}

@media (max-width: 900px) {
  .form-grid,
  .quote-grid,
  .quote-summary {
    grid-template-columns: 1fr;
  }

  .form-card {
    padding: 18px;
  }
}
</style>
