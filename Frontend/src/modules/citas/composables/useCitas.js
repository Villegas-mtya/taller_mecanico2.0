import { ref, computed } from 'vue'
import {
  get_citas,
  create_cita,
  update_cita,
  delete_cita,
  get_clientes_options,
  get_vehiculos_options,
  get_mecanicos_options,
  get_detalle_cita,
  crear_orden_desde_cita,
} from '../services/citas.service'

export function useCitas() {
  const citas = ref([])
  const clientes = ref([])
  const vehiculos = ref([])
  const mecanicos = ref([])

  const cargando = ref(false)
  const guardando = ref(false)
  const editando = ref(false)
  const mostrandoDetalle = ref(false)
  const generandoOrden = ref(false)
  const mostrarForm = ref(false)

  const busqueda = ref('')
  const detalleCita = ref(null)

  const form = ref({
    id: null,
    cliente_id: '',
    vehiculo_id: '',
    fecha: '',
    hora: '',
    estado: 'Pendiente',
    mecanico_id: '',
    tipo_servicio: '',
    notas: '',
  })

  const resetForm = () => {
    form.value = {
      id: null,
      cliente_id: '',
      vehiculo_id: '',
      fecha: '',
      hora: '',
      estado: 'Pendiente',
      mecanico_id: '',
      tipo_servicio: '',
      notas: '',
    }

    editando.value = false
    mostrarForm.value = false
  }

  const fetchCitas = async () => {
    cargando.value = true
    try {
      const data = await get_citas()
      citas.value = Array.isArray(data) ? data : []
    } catch (error) {
      console.error('Error al obtener citas:', error)
      citas.value = []
    } finally {
      cargando.value = false
    }
  }

  const fetchClientes = async () => {
    try {
      const data = await get_clientes_options()
      clientes.value = Array.isArray(data) ? data : []
    } catch (error) {
      console.error('Error al obtener clientes para citas:', error)
      clientes.value = []
    }
  }

  const fetchVehiculos = async () => {
    try {
      const data = await get_vehiculos_options()
      vehiculos.value = Array.isArray(data) ? data : []
    } catch (error) {
      console.error('Error al obtener vehículos para citas:', error)
      vehiculos.value = []
    }
  }

  const fetchMecanicos = async () => {
    try {
      const data = await get_mecanicos_options()
      mecanicos.value = Array.isArray(data) ? data : []
    } catch (error) {
      console.error('Error al obtener mecánicos para citas:', error)
      mecanicos.value = []
    }
  }

  const fetchInitialData = async () => {
    await Promise.all([
      fetchCitas(),
      fetchClientes(),
      fetchVehiculos(),
      fetchMecanicos(),
    ])
  }

  const buildPayload = () => ({
    id: form.value.id,
    cliente_id: form.value.cliente_id || null,
    vehiculo_id: form.value.vehiculo_id || null,
    fecha: form.value.fecha,
    hora: form.value.hora,
    estado: form.value.estado || 'Pendiente',
    mecanico_id: form.value.mecanico_id || null,
    tipo_servicio: form.value.tipo_servicio?.trim() || '',
    notas: form.value.notas?.trim() || '',
  })

  const createCita = async () => {
    if (
      !form.value.cliente_id ||
      !form.value.vehiculo_id ||
      !form.value.fecha ||
      !form.value.hora
    ) return

    guardando.value = true
    try {
      await create_cita(buildPayload())
      resetForm()
      await fetchCitas()
    } catch (error) {
      console.error('Error al crear cita:', error)
      console.error('Payload enviado:', buildPayload())
      console.error('Respuesta backend:', error?.response?.data)
    } finally {
      guardando.value = false
    }
  }

  const updateCita = async () => {
    if (
      !form.value.id ||
      !form.value.cliente_id ||
      !form.value.vehiculo_id ||
      !form.value.fecha ||
      !form.value.hora
    ) return

    guardando.value = true
    try {
      await update_cita(buildPayload())
      resetForm()
      await fetchCitas()
    } catch (error) {
      console.error('Error al actualizar cita:', error)
      console.error('Payload enviado:', buildPayload())
      console.error('Respuesta backend:', error?.response?.data)
    } finally {
      guardando.value = false
    }
  }

  const submitCita = async () => {
    if (editando.value) {
      await updateCita()
    } else {
      await createCita()
    }
  }

  const startEdit = (cita) => {
    form.value = {
      id: cita.id ?? null,
      cliente_id: cita.cliente_id ?? cita.clienteid ?? '',
      vehiculo_id: cita.vehiculo_id ?? cita.vehiculoid ?? '',
      fecha: cita.fecha ?? '',
      hora: cita.hora ?? '',
      estado: cita.estado ?? 'Pendiente',
      mecanico_id: cita.mecanico_id ?? cita.mecanicoid ?? '',
      tipo_servicio: cita.tipo_servicio ?? cita.tiposervicio ?? '',
      notas: cita.notas ?? '',
    }

    editando.value = true
    mostrarForm.value = true
  }

  const removeCita = async (id) => {
    if (!confirm('¿Eliminar cita?')) return

    try {
      await delete_cita(id)
      await fetchCitas()
    } catch (error) {
      console.error('Error al eliminar cita:', error)
    }
  }

  const fetchDetalleCita = async (id) => {
    mostrandoDetalle.value = true
    try {
      detalleCita.value = await get_detalle_cita(id)
    } catch (error) {
      console.error('Error al obtener detalle de cita:', error)
      detalleCita.value = null
    } finally {
      mostrandoDetalle.value = false
    }
  }

  const convertirCitaAOrden = async (citaId, descripcion = '') => {
    generandoOrden.value = true
    try {
      const res = await crear_orden_desde_cita(citaId, descripcion)
      await fetchCitas()
      return res
    } catch (error) {
      console.error('Error al crear orden desde cita:', error)
      throw error
    } finally {
      generandoOrden.value = false
    }
  }

  const citasFiltradas = computed(() => {
    const texto = busqueda.value.toLowerCase().trim()

    if (!texto) return citas.value

    return citas.value.filter((c) => {
      const cliente = (c.clientenombre || c.cliente_nombre || '').toLowerCase()
      const vehiculo = `${c.marca || ''} ${c.modelo || ''} ${c.placas || ''}`.toLowerCase()
      const mecanico = (c.mecaniconombre || c.mecanico_nombre || '').toLowerCase()
      const estado = (c.estado || '').toLowerCase()
      const servicio = (c.tipo_servicio || c.tiposervicio || '').toLowerCase()
      const fecha = (c.fecha || '').toLowerCase()
      const hora = (c.hora || '').toLowerCase()
      const notas = (c.notas || '').toLowerCase()

      return (
        cliente.includes(texto) ||
        vehiculo.includes(texto) ||
        mecanico.includes(texto) ||
        estado.includes(texto) ||
        servicio.includes(texto) ||
        fecha.includes(texto) ||
        hora.includes(texto) ||
        notas.includes(texto)
      )
    })
  })

  return {
    citas,
    clientes,
    vehiculos,
    mecanicos,
    cargando,
    guardando,
    editando,
    mostrandoDetalle,
    generandoOrden,
    mostrarForm,
    busqueda,
    form,
    detalleCita,
    citasFiltradas,
    fetchInitialData,
    fetchCitas,
    fetchClientes,
    fetchVehiculos,
    fetchMecanicos,
    resetForm,
    createCita,
    updateCita,
    submitCita,
    startEdit,
    removeCita,
    fetchDetalleCita,
    convertirCitaAOrden,
  }
}