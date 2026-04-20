<script setup>
import { computed, onMounted, ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import api from "@/services/api";

const route = useRoute();
const router = useRouter();

const cargando = ref(false);
const generandoOrden = ref(false);
const guardandoConcepto = ref(false);
const eliminandoConceptoId = ref(null);
const error = ref("");

const cita = ref(null);
const presupuestoItems = ref([]);
const totalPresupuesto = ref(0);
const ordenRelacionada = ref(null);
const mostrarFormularioConcepto = ref(false);
const editandoConceptoId = ref(null);

const formularioConcepto = ref({
  tipo_item: "servicio",
  descripcion: "",
  cantidad: 1,
  precio_unitario: 0,
});

const citaId = computed(() => route.params.id);

const cargarDetalle = async () => {
  cargando.value = true;
  error.value = "";

  try {
    const { data } = await api.get(`/detalle_cita.php?id=${citaId.value}`);

    if (!data?.success) {
      throw new Error(data?.message || "No se pudo cargar el detalle de la cita");
    }

    cita.value = data.data?.cita ?? null;
    presupuestoItems.value = data.data?.presupuesto_items ?? [];
    totalPresupuesto.value = Number(data.data?.total_presupuesto ?? 0);
    ordenRelacionada.value = data.data?.orden_relacionada ?? null;
  } catch (err) {
    error.value =
      err?.response?.data?.message || err.message || "Error al cargar la cita";
  } finally {
    cargando.value = false;
  }
};

const generarOrden = async () => {
  if (!cita.value || ordenRelacionada.value || presupuestoItems.value.length === 0) return;

  generandoOrden.value = true;
  error.value = "";

  try {
    const { data } = await api.post("/crear_orden_desde_cita.php", {
      cita_id: cita.value.id,
    });

    if (!data?.success) {
      throw new Error(data?.message || "No se pudo generar la orden");
    }

    await cargarDetalle();
  } catch (err) {
    const errorCode = err?.response?.data?.error_code;
    if (errorCode === "PRESUPUESTO_VACIO") {
      error.value =
        "No puedes generar la orden sin conceptos en el presupuesto de la cita.";
    } else {
      error.value =
        err?.response?.data?.message || err.message || "Error al generar la orden";
    }
  } finally {
    generandoOrden.value = false;
  }
};

const resetFormularioConcepto = () => {
  formularioConcepto.value = {
    tipo_item: "servicio",
    descripcion: "",
    cantidad: 1,
    precio_unitario: 0,
  };
  editandoConceptoId.value = null;
  mostrarFormularioConcepto.value = false;
};

const validarFormularioConcepto = () => {
  const descripcion = formularioConcepto.value.descripcion.trim();
  const cantidad = Number(formularioConcepto.value.cantidad);
  const precioUnitario = Number(formularioConcepto.value.precio_unitario);

  if (!descripcion) {
    return "La descripción del concepto es obligatoria.";
  }

  if (!Number.isFinite(cantidad) || cantidad <= 0) {
    return "La cantidad debe ser mayor que 0.";
  }

  if (!Number.isFinite(precioUnitario) || precioUnitario < 0) {
    return "El precio unitario no puede ser negativo.";
  }

  return "";
};

const iniciarNuevoConcepto = () => {
  error.value = "";
  mostrarFormularioConcepto.value = true;
  editandoConceptoId.value = null;
  formularioConcepto.value = {
    tipo_item: "servicio",
    descripcion: "",
    cantidad: 1,
    precio_unitario: 0,
  };
};

const iniciarEdicionConcepto = (item) => {
  error.value = "";
  mostrarFormularioConcepto.value = true;
  editandoConceptoId.value = item.id;
  formularioConcepto.value = {
    tipo_item: item.tipo_item || "servicio",
    descripcion: item.descripcion || "",
    cantidad: Number(item.cantidad || 1),
    precio_unitario: Number(item.precio_unitario || 0),
  };
};

const guardarConcepto = async () => {
  if (!cita.value) return;

  const errorValidacion = validarFormularioConcepto();
  if (errorValidacion) {
    error.value = errorValidacion;
    return;
  }

  guardandoConcepto.value = true;
  error.value = "";

  try {
    const payloadBase = {
      cita_id: cita.value.id,
      tipo_item: formularioConcepto.value.tipo_item,
      descripcion: formularioConcepto.value.descripcion.trim(),
      cantidad: Number(formularioConcepto.value.cantidad),
      precio_unitario: Number(formularioConcepto.value.precio_unitario),
    };

    if (editandoConceptoId.value) {
      await api.post("/presupuesto_items.php", {
        action: "update",
        id: editandoConceptoId.value,
        ...payloadBase,
      });
    } else {
      await api.post("/presupuesto_items.php", {
        action: "create",
        ...payloadBase,
      });
    }

    resetFormularioConcepto();
    await cargarDetalle();
  } catch (err) {
    error.value =
      err?.response?.data?.message || err.message || "Error al guardar el concepto";
  } finally {
    guardandoConcepto.value = false;
  }
};

const eliminarConcepto = async (item) => {
  if (!item?.id) return;
  if (!confirm("¿Eliminar este concepto del presupuesto?")) return;

  eliminandoConceptoId.value = item.id;
  error.value = "";

  try {
    await api.post("/presupuesto_items.php", {
      action: "delete",
      id: item.id,
    });
    await cargarDetalle();
  } catch (err) {
    error.value =
      err?.response?.data?.message || err.message || "Error al eliminar el concepto";
  } finally {
    eliminandoConceptoId.value = null;
  }
};

const volver = () => {
  router.push({ name: "citas" });
};

const irAOrdenes = () => {
  router.push({ name: "ordenes" });
};

const estadoClase = computed(() => {
  const estado = (cita.value?.estado || "").toLowerCase();

  if (estado.includes("confirm")) return "badge badge-info";
  if (estado.includes("diagn")) return "badge badge-warning";
  if (estado.includes("presup")) return "badge badge-primary";
  if (estado.includes("convert")) return "badge badge-success";

  return "badge badge-neutral";
});

const moneda = (valor) => {
  return new Intl.NumberFormat("es-MX", {
    style: "currency",
    currency: "MXN",
  }).format(Number(valor || 0));
};

onMounted(cargarDetalle);
</script>

<template>
  <section class="detalle-page">
    <header class="page-header">
      <div>
        <p class="eyebrow">Detalle</p>
        <h1>Cita #{{ citaId }}</h1>
      </div>

      <div class="header-actions">
        <button type="button" class="btn btn-secondary" @click="volver">Volver</button>
        <button
          v-if="ordenRelacionada"
          type="button"
          class="btn btn-ghost"
          @click="irAOrdenes"
        >
          Ver órdenes
        </button>
      </div>
    </header>

    <div v-if="error" class="alert alert-error">
      {{ error }}
    </div>

    <div v-if="cargando" class="card loading-card">
      Cargando detalle...
    </div>

    <template v-else-if="cita">
      <section class="card cita-card">
        <div class="cita-row">
          <div>
            <h2>{{ cita.cliente_nombre }}</h2>
            <p>
              {{ cita.vehiculo_marca || cita.marca }}
              {{ cita.vehiculo_modelo || cita.modelo }}
              - {{ cita.vehiculo_placas || cita.placas }}
            </p>
          </div>

          <span :class="estadoClase">{{ cita.estado || "Sin estado" }}</span>
        </div>

        <div class="meta-grid">
          <div>
            <strong>Fecha</strong>
            <p>{{ cita.fecha || "-" }}</p>
          </div>
          <div>
            <strong>Hora</strong>
            <p>{{ cita.hora || "-" }}</p>
          </div>
          <div>
            <strong>Mecánico</strong>
            <p>{{ cita.mecanico_nombre || "Sin asignar" }}</p>
          </div>
          <div>
            <strong>Servicio</strong>
            <p>{{ cita.tipo_servicio || "-" }}</p>
          </div>
        </div>
      </section>

      <section class="card presupuesto-card">
        <div class="presupuesto-head">
          <h2>Presupuesto asociado</h2>
          <div class="presupuesto-head-actions">
            <span class="total-pill">Total: {{ moneda(totalPresupuesto) }}</span>
            <button
              v-if="!ordenRelacionada"
              type="button"
              class="btn btn-secondary"
              @click="iniciarNuevoConcepto"
            >
              Agregar concepto
            </button>
          </div>
        </div>

        <form
          v-if="mostrarFormularioConcepto && !ordenRelacionada"
          class="concept-form"
          @submit.prevent="guardarConcepto"
        >
          <div class="concept-grid">
            <div class="field">
              <label for="tipo_item">Tipo</label>
              <select
                id="tipo_item"
                v-model="formularioConcepto.tipo_item"
                class="input"
              >
                <option value="servicio">Servicio</option>
                <option value="inventario">Inventario</option>
              </select>
            </div>

            <div class="field field-grow">
              <label for="descripcion">Descripción</label>
              <input
                id="descripcion"
                v-model="formularioConcepto.descripcion"
                type="text"
                class="input"
                placeholder="Describe el concepto"
              />
            </div>

            <div class="field">
              <label for="cantidad">Cantidad</label>
              <input
                id="cantidad"
                v-model.number="formularioConcepto.cantidad"
                type="number"
                min="1"
                step="1"
                class="input"
              />
            </div>

            <div class="field">
              <label for="precio_unitario">P. unitario</label>
              <input
                id="precio_unitario"
                v-model.number="formularioConcepto.precio_unitario"
                type="number"
                min="0"
                step="0.01"
                class="input"
              />
            </div>
          </div>

          <div class="concept-actions">
            <button type="submit" class="btn btn-primary" :disabled="guardandoConcepto">
              {{
                guardandoConcepto
                  ? "Guardando..."
                  : editandoConceptoId
                    ? "Actualizar concepto"
                    : "Guardar concepto"
              }}
            </button>
            <button type="button" class="btn btn-ghost" @click="resetFormularioConcepto">
              Cancelar
            </button>
          </div>
        </form>

        <div v-if="presupuestoItems.length === 0" class="empty-state">
          <h3>Sin conceptos</h3>
          <p>Agrega conceptos en el módulo de presupuesto para habilitar la generación de orden.</p>
        </div>

        <div v-else class="table-wrapper">
          <table class="presupuesto-table">
            <thead>
              <tr>
                <th>Tipo</th>
                <th>Descripción</th>
                <th>Cantidad</th>
                <th>P. Unitario</th>
                <th>Importe</th>
                <th v-if="!ordenRelacionada">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in presupuestoItems" :key="item.id">
                <td>{{ item.tipo_item || "-" }}</td>
                <td>{{ item.descripcion || "-" }}</td>
                <td>{{ Number(item.cantidad || 0) }}</td>
                <td>{{ moneda(item.precio_unitario) }}</td>
                <td>{{ moneda(item.importe) }}</td>
                <td v-if="!ordenRelacionada">
                  <div class="table-actions">
                    <button type="button" class="btn btn-ghost btn-table" @click="iniciarEdicionConcepto(item)">
                      Editar
                    </button>
                    <button
                      type="button"
                      class="btn btn-danger btn-table"
                      :disabled="eliminandoConceptoId === item.id"
                      @click="eliminarConcepto(item)"
                    >
                      {{ eliminandoConceptoId === item.id ? "Eliminando..." : "Eliminar" }}
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="actions-row">
          <button
            type="button"
            class="btn btn-primary"
            :disabled="generandoOrden || !!ordenRelacionada || presupuestoItems.length === 0"
            @click="generarOrden"
          >
            {{
              ordenRelacionada
                ? "Orden ya generada"
                : generandoOrden
                  ? "Generando orden..."
                  : "Generar orden desde cita"
            }}
          </button>

          <p v-if="ordenRelacionada" class="hint">
            Orden relacionada #{{ ordenRelacionada.id }}
          </p>
        </div>
      </section>
    </template>
  </section>
</template>

<style scoped>
.detalle-page {
  display: grid;
  gap: 20px;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 12px;
}

.eyebrow {
  margin: 0;
  color: #6366f1;
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
}

.page-header h1 {
  margin: 6px 0 0;
  color: #0f172a;
}

.header-actions {
  display: flex;
  gap: 8px;
}

.card {
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  padding: 20px;
}

.cita-row {
  display: flex;
  justify-content: space-between;
  gap: 16px;
  align-items: center;
}

.cita-row h2 {
  margin: 0;
}

.cita-row p {
  margin: 6px 0 0;
  color: #64748b;
}

.meta-grid {
  margin-top: 18px;
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
}

.meta-grid strong {
  color: #334155;
}

.meta-grid p {
  margin: 6px 0 0;
  color: #64748b;
}

.presupuesto-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 10px;
}

.presupuesto-head-actions {
  display: flex;
  gap: 10px;
  align-items: center;
  flex-wrap: wrap;
}

.total-pill {
  background: #eef2ff;
  color: #3730a3;
  border-radius: 999px;
  padding: 6px 12px;
  font-weight: 700;
}

.table-wrapper {
  margin-top: 14px;
  overflow-x: auto;
}

.concept-form {
  margin-top: 12px;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 14px;
  background: #f8fafc;
}

.concept-grid {
  display: grid;
  grid-template-columns: 140px 1fr 130px 140px;
  gap: 10px;
}

.field {
  display: grid;
  gap: 6px;
}

.field label {
  font-size: 12px;
  color: #334155;
  font-weight: 600;
}

.field-grow {
  min-width: 220px;
}

.input {
  min-height: 38px;
  border: 1px solid #cbd5e1;
  border-radius: 10px;
  padding: 8px 10px;
  background: #fff;
}

.concept-actions {
  margin-top: 10px;
  display: flex;
  gap: 8px;
}

.presupuesto-table {
  width: 100%;
  border-collapse: collapse;
}

.presupuesto-table th,
.presupuesto-table td {
  border-bottom: 1px solid #e2e8f0;
  padding: 12px;
  text-align: left;
}

.actions-row {
  margin-top: 14px;
  display: flex;
  gap: 12px;
  align-items: center;
  flex-wrap: wrap;
}

.hint {
  margin: 0;
  color: #64748b;
}

.alert {
  border-radius: 12px;
  padding: 12px 14px;
}

.alert-error {
  background: #fee2e2;
  color: #b91c1c;
  border: 1px solid #fecaca;
}

.badge {
  display: inline-flex;
  align-items: center;
  min-height: 28px;
  padding: 4px 10px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 700;
}

.badge-info {
  color: #1d4ed8;
  background: #dbeafe;
}

.badge-warning {
  color: #a16207;
  background: #fef9c3;
}

.badge-primary {
  color: #4338ca;
  background: #e0e7ff;
}

.badge-success {
  color: #166534;
  background: #dcfce7;
}

.badge-neutral {
  color: #475569;
  background: #e2e8f0;
}

.btn {
  min-height: 40px;
  padding: 8px 14px;
  border: 1px solid transparent;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
}

.btn-primary {
  background: #4f46e5;
  color: white;
}

.btn-secondary {
  background: #e2e8f0;
  color: #0f172a;
}

.btn-ghost {
  background: transparent;
  color: #334155;
  border-color: #cbd5e1;
}

.btn-danger {
  background: #fee2e2;
  color: #b91c1c;
  border-color: #fecaca;
}

.btn-table {
  min-height: 34px;
  padding: 6px 10px;
  font-size: 12px;
}

.table-actions {
  display: flex;
  gap: 6px;
}

.empty-state {
  margin-top: 12px;
  color: #64748b;
}

.empty-state h3 {
  margin: 0;
  color: #0f172a;
}

.empty-state p {
  margin: 8px 0 0;
}

@media (max-width: 900px) {
  .meta-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .concept-grid {
    grid-template-columns: 1fr;
  }
}
</style>
