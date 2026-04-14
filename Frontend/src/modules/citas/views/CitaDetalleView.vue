<script setup>
import { ref, computed, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import api from "@/services/api";

const route = useRoute();
const router = useRouter();

const cargando = ref(false);
const generando_orden = ref(false);
const error = ref("");

const cita = ref(null);
const presupuestoitems = ref([]);
const totalpresupuesto = ref(0);
const ordenrelacionada = ref(null);

const cita_id = computed(() => route.params.id);

const cargar_detalle = async () => {
  cargando.value = true;
  error.value = "";

  try {
    const { data } = await api.get(`/detalle_cita.php?id=${cita_id.value}`);

    if (!data?.success) {
      throw new Error(data?.message || "No se pudo cargar el detalle de la cita");
    }

    cita.value = data.data?.cita ?? null;
    presupuestoitems.value = data.data?.presupuestoitems ?? [];
    totalpresupuesto.value = Number(data.data?.totalpresupuesto ?? 0);
    ordenrelacionada.value = data.data?.ordenrelacionada ?? null;
  } catch (err) {
    error.value = err?.response?.data?.message || err.message || "Error al cargar la cita";
  } finally {
    cargando.value = false;
  }
};

const generar_orden = async () => {
  if (!cita.value || ordenrelacionada.value) return;

  generando_orden.value = true;
  error.value = "";

  try {
    const { data } = await api.post("/crear_orden_desde_cita.php", {
      citaid: cita.value.id,
    });

    if (!data?.success) {
      throw new Error(data?.message || "No se pudo generar la orden");
    }

    await cargar_detalle();
  } catch (err) {
    error.value =
      err?.response?.data?.message || err.message || "Error al generar la orden";
  } finally {
    generando_orden.value = false;
  }
};

const volver = () => {
  router.push({ name: "citas" });
};

const ir_a_orden = () => {
  if (!ordenrelacionada.value?.id) return;
  router.push({ name: "ordenes" });
};

const estado_clase = computed(() => {
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

onMounted(cargar_detalle);
</script>