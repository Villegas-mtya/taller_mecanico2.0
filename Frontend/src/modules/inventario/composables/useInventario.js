import { computed, ref } from "vue";
import {
  getInventario,
  createInventarioItem,
  updateInventarioItem,
  deleteInventarioItem,
} from "../services/inventario.service";

export function useInventario() {
  const inventario = ref([]);
  const cargando = ref(false);
  const guardando = ref(false);
  const editando = ref(false);
  const mostrarForm = ref(false);
  const busqueda = ref("");

  const getInitialForm = () => ({
    id: null,
    nombre: "",
    descripcion: "",
    stock: 0,
    costo: 0,
    activo: 1,
  });

  const form = ref(getInitialForm());

  const resetForm = () => {
    form.value = getInitialForm();
    editando.value = false;
    mostrarForm.value = false;
  };

  const fetchInventario = async () => {
    cargando.value = true;
    try {
      inventario.value = await getInventario();
    } catch (error) {
      console.error("Error al obtener inventario:", error);
      inventario.value = [];
    } finally {
      cargando.value = false;
    }
  };

  const submitInventario = async () => {
    if (!form.value.nombre.trim()) return;

    guardando.value = true;
    try {
      const payload = {
        ...form.value,
        stock: Number(form.value.stock || 0),
        costo: Number(form.value.costo || 0),
        activo: Number(form.value.activo ? 1 : 0),
      };

      if (editando.value) {
        await updateInventarioItem(payload);
      } else {
        await createInventarioItem(payload);
      }

      resetForm();
      await fetchInventario();
    } catch (error) {
      console.error("Error al guardar inventario:", error);
    } finally {
      guardando.value = false;
    }
  };

  const startEdit = (item) => {
    form.value = {
      id: item.id,
      nombre: item.nombre || "",
      descripcion: item.descripcion || "",
      stock: Number(item.stock || 0),
      costo: Number(item.costo || 0),
      activo: Number(item.activo ?? 1),
    };

    editando.value = true;
    mostrarForm.value = true;
  };

  const removeItem = async (id) => {
    if (!confirm("¿Eliminar item de inventario?")) return;

    try {
      await deleteInventarioItem(id);
      await fetchInventario();
    } catch (error) {
      console.error("Error al eliminar item de inventario:", error);
    }
  };

  const inventarioFiltrado = computed(() => {
    const texto = busqueda.value.toLowerCase().trim();
    if (!texto) return inventario.value;

    return inventario.value.filter((item) =>
      String(item.nombre || "").toLowerCase().includes(texto) ||
      String(item.descripcion || "").toLowerCase().includes(texto)
    );
  });

  return {
    inventario,
    cargando,
    guardando,
    editando,
    mostrarForm,
    busqueda,
    form,
    inventarioFiltrado,
    fetchInventario,
    submitInventario,
    startEdit,
    removeItem,
    resetForm,
  };
}
