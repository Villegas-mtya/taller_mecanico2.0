import { ref, computed } from "vue";
import {
  getClientes,
  createCliente,
  updateCliente,
  deleteCliente,
} from "../services/clientes.service";

export function useClientes() {
  const clientes = ref([]);
  const cargando = ref(false);
  const guardando = ref(false);
  const editando = ref(false);
  const mostrarForm = ref(false);
  const busqueda = ref("");

  const form = ref({
    id: null,
    nombre: "",
    telefono: "",
    correo: "",
  });

  const obtenerClientes = async () => {
    cargando.value = true;
    try {
      clientes.value = await getClientes();
    } catch (error) {
      console.error("Error al obtener clientes:", error);
    } finally {
      cargando.value = false;
    }
  };

  const limpiarForm = () => {
    form.value = {
      id: null,
      nombre: "",
      telefono: "",
      correo: "",
    };
    editando.value = false;
    mostrarForm.value = false;
  };

  const crearCliente = async () => {
    if (!form.value.nombre.trim()) return;

    guardando.value = true;
    try {
      await createCliente(form.value);
      limpiarForm();
      await obtenerClientes();
    } catch (error) {
      console.error("Error al crear cliente:", error);
    } finally {
      guardando.value = false;
    }
  };

  const actualizarCliente = async () => {
    if (!form.value.nombre.trim()) return;

    guardando.value = true;
    try {
      await updateCliente(form.value);
      limpiarForm();
      await obtenerClientes();
    } catch (error) {
      console.error("Error al actualizar cliente:", error);
    } finally {
      guardando.value = false;
    }
  };

  const eliminarCliente = async (id) => {
    if (!confirm("¿Eliminar cliente?")) return;

    try {
      await deleteCliente(id);
      await obtenerClientes();
    } catch (error) {
      console.error("Error al eliminar cliente:", error);
    }
  };

  const editarCliente = (cliente) => {
    form.value = {
      id: cliente.id,
      nombre: cliente.nombre || "",
      telefono: cliente.telefono || "",
      correo: cliente.correo || "",
    };
    editando.value = true;
    mostrarForm.value = true;
  };

  const submitCliente = async () => {
    if (editando.value) {
      await actualizarCliente();
    } else {
      await crearCliente();
    }
  };

  const clientesFiltrados = computed(() => {
    const texto = busqueda.value.toLowerCase().trim();

    if (!texto) return clientes.value;

    return clientes.value.filter((c) =>
      (c.nombre || "").toLowerCase().includes(texto) ||
      (c.correo || "").toLowerCase().includes(texto) ||
      (c.telefono || "").toLowerCase().includes(texto)
    );
  });

  return {
    clientes,
    cargando,
    guardando,
    editando,
    mostrarForm,
    busqueda,
    form,
    clientesFiltrados,
    obtenerClientes,
    limpiarForm,
    crearCliente,
    actualizarCliente,
    eliminarCliente,
    editarCliente,
    submitCliente,
  };
}