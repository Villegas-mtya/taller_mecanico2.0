import api from '@/services/api'

export async function get_citas() {
  const { data } = await api.get('/citas.php')
  return data
}

export async function create_cita(payload) {
  const body = {
    action: 'create',
    id: payload.id ?? null,
    cliente_id: payload.cliente_id ?? null,
    vehiculo_id: payload.vehiculo_id ?? null,
    fecha: payload.fecha ?? '',
    hora: payload.hora ?? '',
    estado: payload.estado ?? 'Pendiente',
    mecanico_id: payload.mecanico_id || null,
    tipo_servicio: payload.tipo_servicio ?? '',
    notas: payload.notas ?? '',
  }

  const { data } = await api.post('/citas.php', body)
  return data
}

export async function update_cita(payload) {
  const body = {
    action: 'update',
    id: payload.id,
    cliente_id: payload.cliente_id ?? null,
    vehiculo_id: payload.vehiculo_id ?? null,
    fecha: payload.fecha ?? '',
    hora: payload.hora ?? '',
    estado: payload.estado ?? 'Pendiente',
    mecanico_id: payload.mecanico_id || null,
    tipo_servicio: payload.tipo_servicio ?? '',
    notas: payload.notas ?? '',
  }

  const { data } = await api.post('/citas.php', body)
  return data
}

export async function delete_cita(id) {
  const { data } = await api.post('/citas.php', {
    action: 'delete',
    id,
  })

  return data
}

export async function get_detalle_cita(id) {
  const { data } = await api.get(`/detalle_cita.php?id=${id}`)
  return data
}

export async function crear_orden_desde_cita(cita_id, descripcion = '') {
  const { data } = await api.post('/crear_orden_desde_cita.php', {
    cita_id,
    descripcion,
  })

  return data
}

export async function get_clientes_options() {
  const { data } = await api.get('/clientes.php')
  return Array.isArray(data) ? data : []
}

export async function get_vehiculos_options() {
  const { data } = await api.get('/vehiculos.php')
  return Array.isArray(data) ? data : []
}

export async function get_mecanicos_options() {
  const { data } = await api.get('/mecanicos.php')
  return Array.isArray(data) ? data : []
}