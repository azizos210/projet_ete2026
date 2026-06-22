import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import { ThemeProvider } from '@mui/material/styles';
import CssBaseline from '@mui/material/CssBaseline';
import { AuthProvider } from './contexts/AuthContext';
import { lightTheme } from './theme';
import DashboardLayout from './layouts/DashboardLayout';
import DashboardPage from './pages/DashboardPage';
import ProfilePage from './pages/ProfilePage';
import AdminUsersPage from './pages/admin/AdminUsersPage';
import AdminAuditLogsPage from './pages/admin/AdminAuditLogsPage';
import PatientDossierPage from './pages/patient/PatientDossierPage';
import PatientDashboardPage from './pages/patient/PatientDashboardPage';
import MedecinConsultationsPage from './pages/medecin/MedecinConsultationsPage';
import MedecinRdvPage from './pages/medecin/MedecinRdvPage';

function AppRoutes() {
  return (
    <Routes>
      <Route path="/" element={<Navigate to="/app/dashboard" replace />} />

      <Route path="/app" element={<DashboardLayout />}>
        <Route index element={<Navigate to="/app/dashboard" replace />} />
        <Route path="dashboard" element={<DashboardPage />} />
        <Route path="admin/users" element={<AdminUsersPage />} />
        <Route path="admin/audit" element={<AdminAuditLogsPage />} />
        <Route path="patient/dashboard" element={<PatientDashboardPage />} />
        <Route path="patient/dossier" element={<PatientDossierPage />} />
        <Route path="profile" element={<ProfilePage />} />
        <Route path="medecin/consultations" element={<MedecinConsultationsPage />} />
        <Route path="medecin/rendez-vous" element={<MedecinRdvPage />} />
      </Route>

      <Route path="*" element={<Navigate to="/app/dashboard" replace />} />
    </Routes>
  );
}

export default function App() {
  return (
    <ThemeProvider theme={lightTheme}>
      <CssBaseline />
      <BrowserRouter basename="/front">
        <AuthProvider>
          <AppRoutes />
        </AuthProvider>
      </BrowserRouter>
    </ThemeProvider>
  );
}
