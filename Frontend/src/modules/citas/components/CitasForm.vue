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
  vehiculos: {
    type: Array,
    default: () => [],
  },
  mecanicos: {
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

const emit = defineEmits(["update:modelValue", "submit", "cancel"]);

function updateField(field, value) {
  emit("update:modelValue", {
    ...props.modelValue,
    [field]: value,
  });
}
</script>

<template>
  <BaseCard>
    <div class="form-card">
      <div class="section-head">
        <h2>{{ editando ? "Editar cita" : "Nueva cita" }}</h2>
        <p>Programa una cita y relaciónala con cliente, vehículo y mecánico.</p>
      </div>

      <form class="cita-form" @submit.prevent="emit('submit')">
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
                {{ vehiculo.marca }} {{ vehiculo.modelo }} - {{ vehiculo.placas }}
              </option>
            </select>
          </div>
        </div>

        <div class="form-grid">
          <div class="field">
            <label for="fecha">Fecha</label>
            <input
              id="fecha"
              class="input"
              type="date"
              :value="modelValue.fecha"
              @input="updateField('fecha', $event.target.value)"
              required
            />
          </div>

          <div class="field">
            <label for="hora">Hora</label>
            <input
              id="hora"
              class="input"
              type="time"
              :value="modelValue.hora"
              @input="updateField('hora', $event.target.value)"
              required
            />
          </div>
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
              <option value="Confirmada">Confirmada</option>
              <option value="En diagnóstico">En diagnóstico</option>
              <option value="Presupuestada">Presupuestada</option>
              <option value="Convertida en orden">Convertida en orden</option>
              <option value="Cancelada">Cancelada</option>
            </select>
          </div>

          <div class="field">
            <label for="mecanico_id">Mecánico</label>
            <select
              id="mecanico_id"
              class="input"
              :value="modelValue.mecanico_id"
              @change="updateField('mecanico_id', $event.target.value)"
            >
              <option value="">Sin asignar</option>
              <option
                v-for="mecanico in mecanicos"
                :key="mecanico.id"
                :value="mecanico.id"
              >
                {{ mecanico.nombre }}
              </option>
            </select>
          </div>
        </div>

        <div class="field">
          <label for="tipo_servicio">Tipo de servicio</label>
          <input
            id="tipo_servicio"
            class="input"
            type="text"
            :value="modelValue.tipo_servicio"
            @input="updateField('tipo_servicio', $event.target.value)"
            placeholder="Ej. Diagnóstico general, mantenimiento, reparación"
          />
        </div>

        <div class="field">
          <label for="notas">Notas</label>
          <textarea
            id="notas"
            class="input textarea"
            :value="modelValue.notas"
            @input="updateField('notas', $event.target.value)"
            placeholder="Describe detalles relevantes de la cita"
            rows="5"
          ></textarea>
        </div>

        <div class="form-actions">
          <BaseButton type="submit" :disabled="guardando">
            {{ guardando ? "Guardando..." : editando ? "Actualizar cita" : "Guardar cita" }}
          </BaseButton>

          <BaseButton type="button" variant="secondary" @click="emit('cancel')">
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

.cita-form {
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
  color: #334155;
  font-weight: 600;
  font-size: 14px;
}

.input {
  width: 100%;
  min-height: 48px;
  padding: 12px 14px;
  background: #ffffff;
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

.textarea {
  min-height: 120px;
  resize: vertical;
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
}
</style>