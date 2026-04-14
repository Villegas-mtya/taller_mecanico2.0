<script setup>
import BaseCard from "@/components/base/BaseCard.vue";
import BaseButton from "@/components/base/BaseButton.vue";

const props = defineProps({
  modelValue: {
    type: Object,
    required: true,
  },
  clientes: {
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
          <h2>{{ editando ? "Editar vehículo" : "Nuevo vehículo" }}</h2>
          <p>
            {{ editando
              ? "Actualiza la información del vehículo seleccionado."
              : "Registra un vehículo y relaciónalo con un cliente." }}
          </p>
        </div>
      </div>

      <form class="vehiculo-form" @submit.prevent="emit('submit')">
        <div class="form-grid">
          <div class="field">
            <label for="cliente_id">Cliente</label>
            <select
              id="cliente_id"
              :value="modelValue.cliente_id"
              @change="updateField('cliente_id', $event.target.value)"
              class="input"
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
            <label for="placas">Placas</label>
            <input
              id="placas"
              :value="modelValue.placas"
              @input="updateField('placas', $event.target.value)"
              type="text"
              class="input"
              placeholder="Ej. ABC-123-A"
            />
          </div>
        </div>

        <div class="form-grid">
          <div class="field">
            <label for="marca">Marca</label>
            <input
              id="marca"
              :value="modelValue.marca"
              @input="updateField('marca', $event.target.value)"
              type="text"
              class="input"
              placeholder="Ej. Volkswagen"
              required
            />
          </div>

          <div class="field">
            <label for="modelo">Modelo</label>
            <input
              id="modelo"
              :value="modelValue.modelo"
              @input="updateField('modelo', $event.target.value)"
              type="text"
              class="input"
              placeholder="Ej. Jetta"
              required
            />
          </div>
        </div>

        <div class="form-grid form-grid-single">
          <div class="field">
            <label for="anio">Año</label>
            <input
              id="anio"
              :value="modelValue.anio"
              @input="updateField('anio', $event.target.value)"
              type="number"
              class="input"
              placeholder="Ej. 2020"
              min="1900"
              max="2100"
            />
          </div>
        </div>

        <div class="form-actions">
          <BaseButton type="submit" variant="success" :disabled="guardando">
            {{
              guardando
                ? "Guardando..."
                : editando
                ? "Actualizar vehículo"
                : "Guardar vehículo"
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

.vehiculo-form {
  display: grid;
  gap: 18px;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
}

.form-grid-single {
  grid-template-columns: 1fr;
  max-width: 240px;
}

.field {
  display: grid;
  gap: 8px;
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

  .form-grid,
  .form-grid-single {
    grid-template-columns: 1fr;
    max-width: 100%;
  }
}
</style>