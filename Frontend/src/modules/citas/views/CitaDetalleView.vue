<script setup>
import { computed, onMounted, ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import api from "@/services/api";

const route = useRoute();
const router = useRouter();

const cargando = ref(false);
const generandoOrden = ref(false);
const error = ref("");

const cita = ref(null);
const presupuestoItems = ref([]);
const totalPresupuesto = ref(0);
const ordenRelacionada = ref(null);

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
    error.value =
      err?.response?.data?.message || err.message || "Error al generar la orden";
  } finally {
    generandoOrden.value = false;
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
          <span class="total-pill">Total: {{ moneda(totalPresupuesto) }}</span>
        </div>

        <div v-if="presupuestoItems.length === 0" class="empty-state">
          <h3>Sin conceptos</h3>
          <p>Agrega conceptos en el módulo de presupuesto para habilitar la generación de orden.</p>
        </div>

        <div v-else class="table-wrapper">
          <table class="presupuesto-table">
            <thead>
              <tr>
                <th>Descripción</th>
                <th>Cantidad</th>
                <th>P. Unitario</th>
                <th>Importe</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in presupuestoItems" :key="item.id">
                <td>{{ item.descripcion || "-" }}</td>
                <td>{{ Number(item.cantidad || 0) }}</td>
                <td>{{ moneda(item.precio_unitario) }}</td>
                <td>{{ moneda(item.importe) }}</td>
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
}
</style>
