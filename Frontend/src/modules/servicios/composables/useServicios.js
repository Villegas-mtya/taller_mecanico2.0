import { computed, ref } from "vue";
import {
  getServicios,
  createServicio,
  updateServicio,
  deleteServicio,
} from "../services/servicios.service";

export function useServicios() {
  const servicios = ref([]);
  const cargando = ref(false);
  const guardando = ref(false);
  const editando = ref(false);
  const mostrarForm = ref(false);
  const busqueda = ref("");

  const getInitialForm = () => ({
    id: null,
    nombre: "",
    precio: 0,
    activo: 1,
  });

  const form = ref(getInitialForm());

  const resetForm = () => {
    form.value = getInitialForm();
    editando.value = false;
    mostrarForm.value = false;
  };

  const fetchServicios = async () => {
    cargando.value = true;

    try {
      servicios.value = await getServicios();
    } catch (error) {
      console.error("Error al obtener servicios:", error);
      servicios.value = [];
    } finally {
      cargando.value = false;
    }
  };

  const submitServicio = async () => {
    if (!form.value.nombre.trim()) return;

    guardando.value = true;

    try {
      const payload = {
        ...form.value,
        precio: Number(form.value.precio || 0),
        activo: Number(form.value.activo ? 1 : 0),
      };

      if (editando.value) {
        await updateServicio(payload);
      } else {
        await createServicio(payload);
      }

      resetForm();
      await fetchServicios();
    } catch (error) {
      console.error("Error al guardar servicio:", error);
    } finally {
      guardando.value = false;
    }
  };

  const startEdit = (servicio) => {
    form.value = {
      id: servicio.id,
      nombre: servicio.nombre || "",
      precio: Number(servicio.precio || 0),
      activo: Number(servicio.activo ?? 1),
    };

    editando.value = true;
    mostrarForm.value = true;
  };

  const removeServicio = async (id) => {
    if (!confirm("¿Eliminar servicio?")) return;

    try {
      await deleteServicio(id);
      await fetchServicios();
    } catch (error) {
      console.error("Error al eliminar servicio:", error);
    }
  };

  const serviciosFiltrados = computed(() => {
    const texto = busqueda.value.toLowerCase().trim();
    if (!texto) return servicios.value;

    return servicios.value.filter((s) => String(s.nombre || "").toLowerCase().includes(texto));
  });

  return {
    servicios,
    cargando,
    guardando,
    editando,
    mostrarForm,
    busqueda,
    form,
    serviciosFiltrados,
    fetchServicios,
    submitServicio,
    startEdit,
    removeServicio,
    resetForm,
  };
}
