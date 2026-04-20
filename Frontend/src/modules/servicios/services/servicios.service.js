import api from "@/services/api";

const ENDPOINT = "/servicios.php";

export const getServicios = async () => {
  const { data } = await api.get(ENDPOINT);
  return Array.isArray(data) ? data : [];
};

export const createServicio = async (payload) => {
  const { data } = await api.post(ENDPOINT, {
    action: "create",
    ...payload,
  });

  return data;
};

export const updateServicio = async (payload) => {
  const { data } = await api.post(ENDPOINT, {
    action: "update",
    ...payload,
  });

  return data;
};

export const deleteServicio = async (id) => {
  const { data } = await api.post(ENDPOINT, {
    action: "delete",
    id,
  });

  return data;
};
