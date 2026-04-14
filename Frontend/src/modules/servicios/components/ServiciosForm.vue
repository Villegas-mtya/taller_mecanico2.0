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
        <h2>{{ editando ? "Editar servicio" : "Nuevo servicio" }}</h2>
        <p>Administra los servicios que ofrece el taller.</p>
      </div>
    </div>

    <form class="servicio-form" @submit.prevent="emit('submit')">
      <div class="field">
        <label for="nombre">Nombre *</label>
        <input
          id="nombre"
          type="text"
          class="input"
          :value="modelValue.nombre"
          @input="updateField('nombre', $event.target.value)"
          placeholder="Ej. Afinación mayor"
          required
        />
      </div>

      <div class="field">
        <label for="descripcion">Descripción</label>
        <textarea
          id="descripcion"
          class="input textarea"
          :value="modelValue.descripcion"
          @input="updateField('descripcion', $event.target.value)"
          placeholder="Describe el alcance del servicio"
        />
      </div>

      <div class="form-grid">
        <div class="field">
          <label for="precio_base">Precio base</label>
          <input
            id="precio_base"
            type="number"
            min="0"
            step="0.01"
            class="input"
            :value="modelValue.precio_base"
            @input="updateField('precio_base', $event.target.value)"
          />
        </div>

        <div class="field">
          <label for="duracion_estimada_min">Duración estimada (min)</label>
          <input
            id="duracion_estimada_min"
            type="number"
            min="0"
            step="1"
            class="input"
            :value="modelValue.duracion_estimada_min"
            @input="updateField('duracion_estimada_min', $event.target.value)"
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

      <div class="form-actions">
        <button type="submit" class="btn btn-success" :disabled="guardando">
          {{ guardando ? "Guardando..." : editando ? "Actualizar servicio" : "Guardar servicio" }}
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

.servicio-form {
  display: grid;
  gap: 18px;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
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
  min-height: 110px;
  resize: vertical;
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
  .form-grid {
    grid-template-columns: 1fr;
  }

  .form-card {
    padding: 18px;
  }
}
</style>
