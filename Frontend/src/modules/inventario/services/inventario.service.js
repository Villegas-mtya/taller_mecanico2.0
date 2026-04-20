import api from "@/services/api";

const ENDPOINT = "/inventario.php";

export const getInventario = async () => {
  const { data } = await api.get(ENDPOINT);
  return Array.isArray(data) ? data : [];
};

export const createInventarioItem = async (payload) => {
  const { data } = await api.post(ENDPOINT, {
    action: "create",
    ...payload,
  });
  return data;
};

export const updateInventarioItem = async (payload) => {
  const { data } = await api.post(ENDPOINT, {
    action: "update",
    ...payload,
  });
  return data;
};

export const deleteInventarioItem = async (id) => {
  const { data } = await api.post(ENDPOINT, {
    action: "delete",
    id,
  });
  return data;
};
