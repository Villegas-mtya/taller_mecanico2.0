import { ref, computed } from "vue";
import {
  getVehiculos,
  createVehiculo,
  updateVehiculo,
  deleteVehiculo,
  getClientesOptions,
} from "../services/vehiculos.service";

export function useVehiculos() {
  const vehiculos = ref([]);
  const clientes = ref([]);
  const cargando = ref(false);
  const guardando = ref(false);
  const editando = ref(false);
  const mostrarForm = ref(false);
  const busqueda = ref("");

  const form = ref({
    id: null,
    cliente_id: "",
    marca: "",
    modelo: "",
    placas: "",
    anio: "",
  });

  const resetForm = () => {
    form.value = {
      id: null,
      cliente_id: "",
      marca: "",
      modelo: "",
      placas: "",
      anio: "",
    };
    editando.value = false;
    mostrarForm.value = false;
  };

  const fetchVehiculos = async () => {
    cargando.value = true;
    try {
      vehiculos.value = await getVehiculos();
    } catch (error) {
      console.error("Error al obtener vehículos:", error);
    } finally {
      cargando.value = false;
    }
  };

  const fetchClientes = async () => {
    try {
      clientes.value = await getClientesOptions();
    } catch (error) {
      console.error("Error al obtener clientes:", error);
    }
  };

  const fetchInitialData = async () => {
    await Promise.all([fetchVehiculos(), fetchClientes()]);
  };

  const vehiculosConCliente = computed(() => {
    return vehiculos.value.map((vehiculo) => {
      const cliente = clientes.value.find(
        (c) => String(c.id) === String(vehiculo.cliente_id)
      );

      return {
        ...vehiculo,
        cliente_nombre: vehiculo.cliente_nombre || cliente?.nombre || "Sin cliente",
      };
    });
  });

  const submitVehiculo = async () => {
    if (
      !form.value.cliente_id ||
      !form.value.marca.trim() ||
      !form.value.modelo.trim()
    ) {
      return;
    }

    guardando.value = true;

    try {
      if (editando.value) {
        await updateVehiculo(form.value);
      } else {
        await createVehiculo(form.value);
      }

      resetForm();
      await fetchVehiculos();
    } catch (error) {
      console.error("Error al guardar vehículo:", error);
    } finally {
      guardando.value = false;
    }
  };

  const startEdit = (vehiculo) => {
    form.value = {
      id: vehiculo.id,
      cliente_id: vehiculo.cliente_id || "",
      marca: vehiculo.marca || "",
      modelo: vehiculo.modelo || "",
      placas: vehiculo.placas || "",
      anio: vehiculo.anio || "",
    };
    editando.value = true;
    mostrarForm.value = true;
  };

  const removeVehiculo = async (id) => {
    if (!confirm("¿Eliminar vehículo?")) return;

    try {
      await deleteVehiculo(id);
      await fetchVehiculos();
    } catch (error) {
      console.error("Error al eliminar vehículo:", error);
    }
  };

  const vehiculosFiltrados = computed(() => {
    const texto = busqueda.value.toLowerCase().trim();

    if (!texto) return vehiculosConCliente.value;

    return vehiculosConCliente.value.filter((v) =>
      (v.marca || "").toLowerCase().includes(texto) ||
      (v.modelo || "").toLowerCase().includes(texto) ||
      (v.placas || "").toLowerCase().includes(texto) ||
      (v.anio || "").toString().toLowerCase().includes(texto) ||
      (v.cliente_nombre || "").toLowerCase().includes(texto)
    );
  });

  return {
    vehiculos,
    clientes,
    cargando,
    guardando,
    editando,
    mostrarForm,
    busqueda,
    form,
    vehiculosFiltrados,
    fetchInitialData,
    submitVehiculo,
    startEdit,
    removeVehiculo,
    resetForm,
  };
}