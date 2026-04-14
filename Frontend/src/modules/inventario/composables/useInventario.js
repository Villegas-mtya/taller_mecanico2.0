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
    codigo: "",
    nombre: "",
    categoria: "",
    unidad: "",
    stock_actual: 0,
    stock_minimo: 0,
    costo_unitario: 0,
    precio_venta: 0,
    ubicacion: "",
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
        stock_actual: Number(form.value.stock_actual || 0),
        stock_minimo: Number(form.value.stock_minimo || 0),
        costo_unitario: Number(form.value.costo_unitario || 0),
        precio_venta: Number(form.value.precio_venta || 0),
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
      codigo: item.codigo || "",
      nombre: item.nombre || "",
      categoria: item.categoria || "",
      unidad: item.unidad || "",
      stock_actual: Number(item.stock_actual || 0),
      stock_minimo: Number(item.stock_minimo || 0),
      costo_unitario: Number(item.costo_unitario || 0),
      precio_venta: Number(item.precio_venta || 0),
      ubicacion: item.ubicacion || "",
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
      String(item.codigo || "").toLowerCase().includes(texto) ||
      String(item.nombre || "").toLowerCase().includes(texto) ||
      String(item.categoria || "").toLowerCase().includes(texto) ||
      String(item.ubicacion || "").toLowerCase().includes(texto)
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
