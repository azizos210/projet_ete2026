import axios from 'axios';
import { clearStoredAuth, getStoredToken, isJwtExpired, getStoredRefreshToken, setStoredToken, setStoredRefreshToken } from './token';

const api = axios.create({
  baseURL: '',
  headers: {
    'Content-Type': 'application/json',
  },
});

// A helper axios instance for refresh calls to avoid interceptor recursion
const raw = axios.create({ baseURL: '' });

api.interceptors.request.use(async (config) => {
  const token = getStoredToken();
  const isLoginRequest = typeof config.url === 'string' && config.url.includes('/api/login_check');
  const isRefreshRequest = typeof config.url === 'string' && config.url.includes('/api/auth/refresh');

  // Never attach auth header to login or refresh endpoints
  if (isLoginRequest || isRefreshRequest) return config;

  if (!token) return config;

  // If token expired, try to refresh it using the refresh token
  if (isJwtExpired(token)) {
    const refreshToken = getStoredRefreshToken();
    if (!refreshToken) {
      clearStoredAuth();
      return config;
    }

    try {
      const resp = await raw.post('/api/auth/refresh', { refresh_token: refreshToken });
      const newToken = resp.data?.token || resp.data?.access_token || resp.data?.accessToken;
      const newRefresh = resp.data?.refresh_token || resp.data?.refreshToken;
      if (newToken) {
        setStoredToken(newToken);
        if (newRefresh) setStoredRefreshToken(newRefresh);
        config.headers = config.headers || {};
        config.headers.Authorization = `Bearer ${newToken}`;
      } else {
        clearStoredAuth();
      }
    } catch (e) {
      clearStoredAuth();
      window.location.href = '/front/';
      return Promise.reject(e);
    }

    return config;
  }

  config.headers = config.headers || {};
  config.headers.Authorization = `Bearer ${token}`;
  return config;
}, (error) => Promise.reject(error));

api.interceptors.response.use(
  (response) => response,
  async (error) => {
    const status = error.response?.status;
    const originalRequest = error.config;
    const requestUrl = originalRequest?.url;

    if (status === 401 && originalRequest && typeof requestUrl === 'string' && !requestUrl.includes('/api/login_check')) {
      // prevent infinite retry loops
      if ((originalRequest as any)._retry) {
        clearStoredAuth();
        window.location.href = '/front/';
        return Promise.reject(error);
      }
      (originalRequest as any)._retry = true;

      const refreshToken = getStoredRefreshToken();
      if (!refreshToken) {
        clearStoredAuth();
        window.location.href = '/front/';
        return Promise.reject(error);
      }

      try {
        const resp = await raw.post('/api/auth/refresh', { refresh_token: refreshToken });
        const newToken = resp.data?.token || resp.data?.access_token || resp.data?.accessToken;
        const newRefresh = resp.data?.refresh_token || resp.data?.refreshToken;
        if (!newToken) {
          clearStoredAuth();
          window.location.href = '/front/';
          return Promise.reject(error);
        }

        setStoredToken(newToken);
        if (newRefresh) setStoredRefreshToken(newRefresh);
        originalRequest.headers = originalRequest.headers || {};
        originalRequest.headers.Authorization = `Bearer ${newToken}`;
        return api(originalRequest);
      } catch (e) {
        clearStoredAuth();
        window.location.href = '/front/';
        return Promise.reject(e);
      }
    }

    return Promise.reject(error);
  }
);

export default api;
