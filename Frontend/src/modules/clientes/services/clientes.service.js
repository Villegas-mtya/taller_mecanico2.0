import api from "@/services/api";

export const getClientes = async () => {
  const res = await api.get("clientes.php");
  return Array.isArray(res.data) ? res.data : [];
};

export const createCliente = async (payload) => {
  return await api.post("clientes.php", {
    ...payload,
    action: "create",
  });
};

export const updateCliente = async (payload) => {
  return await api.post("clientes.php", {
    ...payload,
    action: "update",
  });
};

export const deleteCliente = async (id) => {
  return await api.post("clientes.php", {
    id,
    action: "delete",
  });
};