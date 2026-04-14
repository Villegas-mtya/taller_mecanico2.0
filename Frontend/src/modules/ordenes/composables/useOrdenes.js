import { ref, computed } from "vue";
import {
  getOrdenes,
  createOrden,
  updateOrden,
  deleteOrden,
  getClientesOptions,
  getVehiculosOptions,
} from "../services/ordenes.service";

export default function useOrdenes() {
  const ordenes = ref([])
  const clientes = ref([])
  const vehiculos = ref([])
  const cargando = ref(false)
  const guardando = ref(false)
  const editando = ref(false)
  const mostrarForm = ref(false)
  const busqueda = ref('')

  const getInitialForm = () => ({
    id: null,
    cliente_id: '',
    vehiculo_id: '',
    descripcion: '',
    estado: 'Pendiente',
    total: 0,
  })

  const form = ref(getInitialForm())

  const resetForm = () => {
    form.value = getInitialForm()
    editando.value = false
    mostrarForm.value = false
  }

  const fetchOrdenes = async () => {
    cargando.value = true
    try {
      const data = await getOrdenes()
      ordenes.value = Array.isArray(data) ? data : []
    } catch (error) {
      console.error('Error al obtener órdenes:', error)
      ordenes.value = []
    } finally {
      cargando.value = false
    }
  }

  const fetchClientes = async () => {
    try {
      clientes.value = await getClientesOptions()
    } catch (error) {
      console.error('Error al obtener clientes:', error)
      clientes.value = []
    }
  }

  const fetchVehiculos = async () => {
    try {
      vehiculos.value = await getVehiculosOptions()
    } catch (error) {
      console.error('Error al obtener vehículos:', error)
      vehiculos.value = []
    }
  }

  const fetchInitialData = async () => {
    await Promise.all([
      fetchOrdenes(),
      fetchClientes(),
      fetchVehiculos(),
    ])
  }

  const submitOrden = async () => {
    if (!form.value.cliente_id || !form.value.vehiculo_id || !form.value.descripcion.trim()) {
      return
    }

    guardando.value = true
    try {
      const payload = {
        ...form.value,
        total: Number(form.value.total || 0),
      }

      if (editando.value) {
        await updateOrden(payload)
      } else {
        await createOrden(payload)
      }

      await fetchOrdenes()
      resetForm()
    } catch (error) {
      console.error('Error al guardar orden:', error)
    } finally {
      guardando.value = false
    }
  }

  const startEdit = (orden) => {
    form.value = {
      id: orden.id,
      cliente_id: orden.cliente_id ?? '',
      vehiculo_id: orden.vehiculo_id ?? '',
      descripcion: orden.descripcion ?? '',
      estado: orden.estado ?? 'Pendiente',
      total: Number(orden.total ?? 0),
    }

    editando.value = true
    mostrarForm.value = true
  }

  const removeOrden = async (id) => {
    if (!confirm('¿Eliminar orden?')) return

    try {
      await deleteOrden(id)
      await fetchOrdenes()
    } catch (error) {
      console.error('Error al eliminar orden:', error)
    }
  }

  const ordenesFiltradas = computed(() => {
    const texto = busqueda.value.toLowerCase().trim()
    if (!texto) return ordenes.value

    return ordenes.value.filter((o) =>
      String(o.clientenombre ?? '').toLowerCase().includes(texto) ||
      String(o.vehiculonombre ?? '').toLowerCase().includes(texto) ||
      String(o.descripcion ?? '').toLowerCase().includes(texto) ||
      String(o.estado ?? '').toLowerCase().includes(texto)
    )
  })

  return {
    ordenes,
    clientes,
    vehiculos,
    cargando,
    guardando,
    editando,
    mostrarForm,
    busqueda,
    form,
    ordenesFiltradas,
    fetchInitialData,
    submitOrden,
    startEdit,
    removeOrden,
    resetForm,
  }
}