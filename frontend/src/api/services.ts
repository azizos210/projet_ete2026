import api from '../api/client';
import type {
  PatientDashboardData,
  MedecinDashboardData,
  AdminDashboardData,
  RendezVous,
  Consultation,
  Prescription,
  DossierMedical,
  Utilisateur,
  AuditLogEntry,
} from '../types';

const AUTH_BASE = '/api/auth';
const PATIENT_BASE = '/api/patient';
const MEDECIN_BASE = '/api/medecin';
const ADMIN_BASE = '/api/admin';

export const authApi = {
  login: async (email: string, password: string) => {
    const response = await api.post('/api/login_check', { email, password });
    return response.data;
  },
  register: async (data: { email: string; password: string; firstName: string; lastName: string; role?: string }) => {
    const response = await api.post(`${AUTH_BASE}/register`, data);
    return response.data;
  },
  me: async () => {
    const response = await api.get(`${AUTH_BASE}/me`);
    return response.data;
  },
};

export const profileApi = {
  update: async (data: { firstName?: string; lastName?: string; email?: string }) => {
    const response = await api.put(`${AUTH_BASE}/profile`, data);
    return response.data;
  },
};

export const patientApi = {
  getDashboard: async (): Promise<PatientDashboardData> => {
    const response = await api.get(`${PATIENT_BASE}/dashboard`);
    return response.data;
  },
  getRendezVous: async (): Promise<RendezVous[]> => {
    const response = await api.get(`${PATIENT_BASE}/rdv`);
    return response.data;
  },
  getDossierMedical: async (): Promise<DossierMedical> => {
    const response = await api.get(`${PATIENT_BASE}/dossier-medical`);
    return response.data;
  },
};

export const medecinApi = {
  getDashboard: async (): Promise<MedecinDashboardData> => {
    const response = await api.get(`${MEDECIN_BASE}/dashboard`);
    return response.data;
  },
  getConsultations: async (): Promise<Consultation[]> => {
    const response = await api.get(`${MEDECIN_BASE}/consultations`);
    return response.data;
  },
  getPatients: async (): Promise<any[]> => {
    const response = await api.get(`${MEDECIN_BASE}/patients`);
    return response.data;
  },
  getRendezVous: async (): Promise<RendezVous[]> => {
    const response = await api.get(`${MEDECIN_BASE}/rendez-vous`);
    return response.data;
  },
  getPrescriptions: async (): Promise<Prescription[]> => {
    const response = await api.get(`${MEDECIN_BASE}/prescriptions`);
    return response.data;
  },
};

export const adminApi = {
  getDashboard: async (): Promise<AdminDashboardData> => {
    const response = await api.get(`${ADMIN_BASE}/dashboard`);
    return response.data;
  },
  getUtilisateurs: async (): Promise<Utilisateur[]> => {
    const response = await api.get(`${ADMIN_BASE}/utilisateurs`);
    return response.data;
  },
  getStatistiques: async (): Promise<any> => {
    const response = await api.get(`${ADMIN_BASE}/statistiques`);
    return response.data;
  },
  getAuditLogs: async (): Promise<AuditLogEntry[]> => {
    const response = await api.get(`${ADMIN_BASE}/audit-logs`);
    return response.data;
  },
  getMedecins: async (): Promise<any[]> => {
    const response = await api.get(`${ADMIN_BASE}/medecins`);
    return response.data;
  },
  getPatients: async (): Promise<any[]> => {
    const response = await api.get(`${ADMIN_BASE}/patients`);
    return response.data;
  },
};
