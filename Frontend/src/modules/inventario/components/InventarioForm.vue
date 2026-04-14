<script setup>
const props = defineProps({
  modelValue: {
    type: Object,
    required: true,
  },
  editando: {
    type: Boolean,
    default: false,
  },
  guardando: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(["update:modelValue", "submit", "cancel"]);

const updateField = (field, value) => {
  emit("update:modelValue", {
    ...props.modelValue,
    [field]: value,
  });
};
</script>

<template>
  <section class="card form-card">
    <div class="section-head">
      <div>
        <h2>{{ editando ? "Editar item" : "Nuevo item" }}</h2>
        <p>Registra refacciones, materiales y consumibles del taller.</p>
      </div>
    </div>

    <form class="inventario-form" @submit.prevent="emit('submit')">
      <div class="form-grid form-grid--3">
        <div class="field">
          <label for="codigo">Código</label>
          <input
            id="codigo"
            type="text"
            class="input"
            :value="modelValue.codigo"
            @input="updateField('codigo', $event.target.value)"
            placeholder="Ej. FIL-001"
          />
        </div>

        <div class="field">
          <label for="nombre">Nombre *</label>
          <input
            id="nombre"
            type="text"
            class="input"
            :value="modelValue.nombre"
            @input="updateField('nombre', $event.target.value)"
            placeholder="Nombre del producto"
            required
          />
        </div>

        <div class="field">
          <label for="categoria">Categoría</label>
          <input
            id="categoria"
            type="text"
            class="input"
            :value="modelValue.categoria"
            @input="updateField('categoria', $event.target.value)"
            placeholder="Ej. Aceites"
          />
        </div>
      </div>

      <div class="form-grid form-grid--4">
        <div class="field">
          <label for="unidad">Unidad</label>
          <input
            id="unidad"
            type="text"
            class="input"
            :value="modelValue.unidad"
            @input="updateField('unidad', $event.target.value)"
            placeholder="pz, lt, kg"
          />
        </div>

        <div class="field">
          <label for="stock_actual">Stock actual</label>
          <input
            id="stock_actual"
            type="number"
            min="0"
            step="1"
            class="input"
            :value="modelValue.stock_actual"
            @input="updateField('stock_actual', $event.target.value)"
          />
        </div>

        <div class="field">
          <label for="stock_minimo">Stock mínimo</label>
          <input
            id="stock_minimo"
            type="number"
            min="0"
            step="1"
            class="input"
            :value="modelValue.stock_minimo"
            @input="updateField('stock_minimo', $event.target.value)"
          />
        </div>

        <div class="field checkbox-field">
          <label for="activo">Activo</label>
          <input
            id="activo"
            type="checkbox"
            class="checkbox"
            :checked="Number(modelValue.activo) === 1"
            @change="updateField('activo', $event.target.checked ? 1 : 0)"
          />
        </div>
      </div>

      <div class="form-grid form-grid--3">
        <div class="field">
          <label for="costo_unitario">Costo unitario</label>
          <input
            id="costo_unitario"
            type="number"
            min="0"
            step="0.01"
            class="input"
            :value="modelValue.costo_unitario"
            @input="updateField('costo_unitario', $event.target.value)"
          />
        </div>

        <div class="field">
          <label for="precio_venta">Precio de venta</label>
          <input
            id="precio_venta"
            type="number"
            min="0"
            step="0.01"
            class="input"
            :value="modelValue.precio_venta"
            @input="updateField('precio_venta', $event.target.value)"
          />
        </div>

        <div class="field">
          <label for="ubicacion">Ubicación</label>
          <input
            id="ubicacion"
            type="text"
            class="input"
            :value="modelValue.ubicacion"
            @input="updateField('ubicacion', $event.target.value)"
            placeholder="Ej. Estante A-03"
          />
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-success" :disabled="guardando">
          {{ guardando ? "Guardando..." : editando ? "Actualizar item" : "Guardar item" }}
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

.inventario-form {
  display: grid;
  gap: 18px;
}

.form-grid {
  display: grid;
  gap: 16px;
}

.form-grid--3 {
  grid-template-columns: repeat(3, 1fr);
}

.form-grid--4 {
  grid-template-columns: repeat(4, 1fr);
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

.checkbox-field {
  align-content: end;
}

.checkbox {
  width: 20px;
  height: 20px;
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
  .form-grid--3,
  .form-grid--4 {
    grid-template-columns: 1fr;
  }

  .form-card {
    padding: 18px;
  }
}
</style>
