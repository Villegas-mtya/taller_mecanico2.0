<script setup>
import BaseCard from "@/components/base/BaseCard.vue";
import BaseButton from "@/components/base/BaseButton.vue";

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

const emit = defineEmits([
  "update:modelValue",
  "submit",
  "cancel",
]);

const updateField = (field, value) => {
  emit("update:modelValue", {
    ...props.modelValue,
    [field]: value,
  });
};
</script>

<template>
  <BaseCard>
    <div class="form-card">
      <div class="section-head">
        <div>
          <h2>{{ editando ? "Editar cliente" : "Nuevo cliente" }}</h2>
          <p>
            {{ editando
              ? "Actualiza la información del cliente seleccionado."
              : "Registra un nuevo cliente con sus datos básicos." }}
          </p>
        </div>
      </div>

      <form class="cliente-form" @submit.prevent="emit('submit')">
        <div class="form-grid">
          <div class="field field-full">
            <label for="nombre">Nombre completo</label>
            <input
              id="nombre"
              :value="modelValue.nombre"
              @input="updateField('nombre', $event.target.value)"
              type="text"
              class="input"
              placeholder="Ej. Alejandro Villegas Montoya"
              required
            />
          </div>
        </div>

        <div class="form-grid">
          <div class="field">
            <label for="telefono">Teléfono</label>
            <input
              id="telefono"
              :value="modelValue.telefono"
              @input="updateField('telefono', $event.target.value)"
              type="text"
              class="input"
              placeholder="Ej. 9981234567"
            />
          </div>

          <div class="field">
            <label for="correo">Correo electrónico</label>
            <input
              id="correo"
              :value="modelValue.correo"
              @input="updateField('correo', $event.target.value)"
              type="email"
              class="input"
              placeholder="Ej. correo@ejemplo.com"
            />
          </div>
        </div>

        <div class="form-actions">
          <BaseButton type="submit" variant="success" :disabled="guardando">
            {{
              guardando
                ? "Guardando..."
                : editando
                ? "Actualizar cliente"
                : "Guardar cliente"
            }}
          </BaseButton>

          <BaseButton
            type="button"
            variant="secondary"
            @click="emit('cancel')"
          >
            Cancelar
          </BaseButton>
        </div>
      </form>
    </div>
  </BaseCard>
</template>

<style scoped>
.form-card {
  padding: 24px;
}

.section-head {
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

.cliente-form {
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

.field-full {
  grid-column: 1 / -1;
}

.field label {
  color: #334155;
  font-weight: 600;
  font-size: 14px;
}

.input {
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

.input:focus {
  border-color: #6366f1;
  box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.12);
}

.form-actions {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
  margin-top: 4px;
}

@media (max-width: 768px) {
  .form-card {
    padding: 18px;
  }

  .form-grid {
    grid-template-columns: 1fr;
  }

  .field-full {
    grid-column: auto;
  }
}
</style>