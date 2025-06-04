import axios from "axios";
import Cookies from "js-cookie";

const API = axios.create({
  baseURL: "http://localhost:8000",
  withCredentials: true,
});

// Interceptor: เพิ่ม X-XSRF-TOKEN จาก cookie
API.interceptors.request.use((config) => {
  const xsrfToken = Cookies.get("XSRF-TOKEN");
  if (xsrfToken) {
    config.headers["X-XSRF-TOKEN"] = decodeURIComponent(xsrfToken);
  }
  return config;
});

export const getCSRFToken = () => API.get("/sanctum/csrf-cookie");
export const getProducts = () => API.get("/api/products");
export const insertCoin = (data) => API.post("/api/insert-coin", data);
export const purchase = (data) => API.post("/api/purchase", data);
export const getStatus = () => API.get("/api/status");
export const getChangeOptions = () => API.get("/api/change-options");

export default API;
