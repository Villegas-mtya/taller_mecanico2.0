import api from "@/services/api";

export const getVehiculos = async () => {
  const res = await api.get("vehiculos.php");
  return Array.isArray(res.data) ? res.data : [];
};

export const createVehiculo = async (payload) => {
  return await api.post("vehiculos.php", {
    ...payload,
    action: "create",
  });
};

export const updateVehiculo = async (payload) => {
  return await api.post("vehiculos.php", {
    ...payload,
    action: "update",
  });
};

export const deleteVehiculo = async (id) => {
  return await api.post("vehiculos.php", {
    id,
    action: "delete",
  });
};

export const getClientesOptions = async () => {
  const res = await api.get("clientes.php");
  return Array.isArray(res.data) ? res.data : [];
};