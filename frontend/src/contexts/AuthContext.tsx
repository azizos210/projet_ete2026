import { createContext, useContext, useState, useEffect, useCallback, type ReactNode } from 'react';
import type { User } from '../types';
import { authApi } from '../api/services';
import { clearStoredAuth, getStoredToken, isJwtExpired, setStoredToken, setStoredRefreshToken } from '../api/token';

interface AuthContextType {
  user: User | null;
  token: string | null;
  loading: boolean;
  login: (email: string, password: string) => Promise<void>;
  register: (data: { email: string; password: string; firstName: string; lastName: string }) => Promise<void>;
  logout: () => void;
  hasRole: (role: string) => boolean;
}

const AuthContext = createContext<AuthContextType | null>(null);

export function AuthProvider({ children }: { children: ReactNode }) {
  const [user, setUser] = useState<User | null>(() => {
    const stored = localStorage.getItem('user');
    return stored ? JSON.parse(stored) : null;
  });
  const [token, setToken] = useState<string | null>(() => {
    const storedToken = getStoredToken();
    if (storedToken && isJwtExpired(storedToken)) {
      clearStoredAuth();
      return null;
    }
    return storedToken;
  });
  const [loading, setLoading] = useState(false);

  const login = useCallback(async (email: string, password: string) => {
    setLoading(true);
    try {
      // Ensure no stale token is attached to the login request
      clearStoredAuth();
      const data = await authApi.login(email, password);
      const tokenData = data.token || data.access_token || data.accessToken;
      const refresh = data.refresh_token || data.refreshToken;

      if (!tokenData) {
        throw new Error('Authentication failed: no token returned');
      }

      setStoredToken(tokenData);
      if (refresh) setStoredRefreshToken(refresh);
      setToken(tokenData);

      const userData = await authApi.me();
      localStorage.setItem('user', JSON.stringify(userData));
      setUser(userData);
    } catch (err: any) {
      clearStoredAuth();
      setToken(null);
      setUser(null);
      const msg = err?.response?.data?.message || err?.message || 'Une erreur est survenue';
      throw new Error(msg);
    } finally {
      setLoading(false);
    }
  }, []);

  const register = useCallback(async (data: { email: string; password: string; firstName: string; lastName: string }) => {
    setLoading(true);
    try {
      await authApi.register(data);
    } finally {
      setLoading(false);
    }
  }, []);

  const logout = useCallback(() => {
    clearStoredAuth();
    setToken(null);
    setUser(null);
  }, []);

  const hasRole = useCallback((role: string) => {
    return user?.roles?.includes(role) ?? false;
  }, [user]);

  useEffect(() => {
    if (token && !user) {
      if (isJwtExpired(token)) {
        logout();
        return;
      }

      authApi.me()
        .then((userData) => {
          setUser(userData);
          localStorage.setItem('user', JSON.stringify(userData));
        })
        .catch(() => {
          logout();
        });
    }
  }, [token, user, logout]);

  return (
    <AuthContext.Provider value={{ user, token, loading, login, register, logout, hasRole }}>
      {children}
    </AuthContext.Provider>
  );
}

export function useAuth() {
  const ctx = useContext(AuthContext);
  if (!ctx) throw new Error('useAuth must be used within AuthProvider');
  return ctx;
}
