import api from '@/services/api'

const ENDPOINT = '/ordenes.php'

export async function getOrdenes() {
  const { data } = await api.get(ENDPOINT)
  return Array.isArray(data) ? data : []
}

export async function createOrden(payload) {
  const { data } = await api.post(ENDPOINT, {
    action: 'create',
    ...payload,
  })
  return data
}

export async function updateOrden(payload) {
  const { data } = await api.post(ENDPOINT, {
    action: 'update',
    ...payload,
  })
  return data
}

export async function deleteOrden(id) {
  const { data } = await api.post(ENDPOINT, {
    action: 'delete',
    id,
  })
  return data
}

export async function getClientesOptions() {
  const { data } = await api.get('/clientes.php')
  return Array.isArray(data) ? data : []
}

export async function getVehiculosOptions() {
  const { data } = await api.get('/vehiculos.php')
  return Array.isArray(data) ? data : []
}