import { ref, computed } from "vue";
import {
  getOrdenes,
  createOrden,
  updateOrden,
  deleteOrden,
  getClientesOptions,
  getVehiculosOptions,
  getInventarioOptions,
  getServiciosOptions,
} from "../services/ordenes.service";

export default function useOrdenes() {
  const ordenes = ref([])
  const clientes = ref([])
  const vehiculos = ref([])
  const inventario = ref([])
  const servicios = ref([])
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
    inventarioItems: [],
    serviciosItems: [],
    cotizacionActiva: false,
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

  const fetchInventario = async () => {
    try {
      inventario.value = await getInventarioOptions()
    } catch (error) {
      console.error('Error al obtener inventario:', error)
      inventario.value = []
    }
  }

  const fetchServicios = async () => {
    try {
      servicios.value = await getServiciosOptions()
    } catch (error) {
      console.error('Error al obtener servicios:', error)
      servicios.value = []
    }
  }

  const fetchInitialData = async () => {
    await Promise.all([
      fetchOrdenes(),
      fetchClientes(),
      fetchVehiculos(),
      fetchInventario(),
      fetchServicios(),
    ])
  }

  // Calcula subtotales de forma defensiva para evitar NaN cuando falten cantidades o precios.
  const calculateItemsTotal = (items = []) => items.reduce((total, item) => {
    const cantidad = Number(item.cantidad || 0)
    const precio = Number(item.precio || 0)
    return total + cantidad * precio
  }, 0)

  const calculateOrdenTotal = () => {
    const inventarioTotal = calculateItemsTotal(form.value.inventarioItems)
    const serviciosTotal = calculateItemsTotal(form.value.serviciosItems)
    const totalSeleccionado = inventarioTotal + serviciosTotal

    const totalFinal = form.value.cotizacionActiva ? totalSeleccionado : totalSeleccionado || form.value.total || 0

    return Math.round((Number(totalFinal) + Number.EPSILON) * 100) / 100
  }

  const submitOrden = async () => {
    if (!form.value.cliente_id || !form.value.vehiculo_id || !form.value.descripcion.trim()) {
      return
    }

    guardando.value = true
    try {
      const payload = {
        ...form.value,
        total: calculateOrdenTotal(),
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
      inventarioItems: [],
      serviciosItems: [],
      cotizacionActiva: false,
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
    inventario,
    servicios,
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