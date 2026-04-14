<script setup>
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

const updateField = (field, value) => {
  emit('update:modelValue', {
    ...props.modelValue,
    [field]: value,
  })
}
</script>

<template>
  <section class="card form-card">
    <div class="section-head">
      <div>
        <h2>{{ editando ? 'Editar orden' : 'Nueva orden' }}</h2>
        <p>Registra una orden de servicio asociada a un cliente y un vehículo.</p>
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
          <label for="total">Total</label>
          <input
            id="total"
            class="input"
            type="number"
            min="0"
            step="0.01"
            :value="modelValue.total"
            @input="updateField('total', $event.target.value)"
            placeholder="0.00"
          />
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

.form-grid {
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

@media (max-width: 768px) {
  .form-grid {
    grid-template-columns: 1fr;
  }

  .form-card {
    padding: 18px;
  }
}
</style>