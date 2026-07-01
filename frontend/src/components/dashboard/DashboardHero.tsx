import { useNavigate } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import Box from '@mui/material/Box';
import Typography from '@mui/material/Typography';
import Button from '@mui/material/Button';
import { useAuth } from '../../contexts/AuthContext';

import CalendarMonthIcon from '@mui/icons-material/CalendarMonth';
import DescriptionIcon from '@mui/icons-material/Description';
import MedicalServicesIcon from '@mui/icons-material/MedicalServices';
import MessageIcon from '@mui/icons-material/Message';
import LocalHospitalIcon from '@mui/icons-material/LocalHospital';

const BG_IMAGE = `url(${import.meta.env.BASE_URL}bg-dashboard.png)`;

export default function DashboardHero() {
  const { t } = useTranslation();
  const { user, hasRole } = useAuth();
  const navigate = useNavigate();

  const actions = [
    ...(hasRole('ROLE_PATIENT')
      ? [
          { label: t('nav.appointments'), icon: <CalendarMonthIcon />, path: '/app/patient/rdv', color: '#006D77' },
          { label: t('nav.medicalRecords'), icon: <DescriptionIcon />, path: '/app/patient/dossier', color: '#0288D1' },
          { label: t('nav.messages'), icon: <MessageIcon />, path: '/app/patient/messages', color: '#2E7D32' },
        ]
      : []),
    ...(hasRole('ROLE_MEDECIN')
      ? [
          { label: t('nav.consultations'), icon: <MedicalServicesIcon />, path: '/app/medecin/consultations', color: '#006D77' },
          { label: t('nav.appointments'), icon: <CalendarMonthIcon />, path: '/app/medecin/rendez-vous', color: '#0288D1' },
          { label: t('nav.prescriptions'), icon: <DescriptionIcon />, path: '/app/medecin/prescriptions', color: '#2E7D32' },
        ]
      : []),
    ...(hasRole('ROLE_ADMIN')
      ? [
          { label: t('nav.users'), icon: <LocalHospitalIcon />, path: '/app/admin/users', color: '#006D77' },
          { label: t('nav.patients'), icon: <MedicalServicesIcon />, path: '/app/admin/patients', color: '#0288D1' },
          { label: t('nav.analytics'), icon: <DescriptionIcon />, path: '/app/admin/analytics', color: '#2E7D32' },
        ]
      : []),
    ...(hasRole('ROLE_INFIRMIER')
      ? [
          { label: t('nav.consultations'), icon: <MedicalServicesIcon />, path: '/app/infirmier', color: '#006D77' },
          { label: t('nav.calendar'), icon: <CalendarMonthIcon />, path: '/app/infirmier', color: '#E29578' },
        ]
      : []),
  ];

  return (
    <Box
      sx={{
        position: 'relative',
        borderRadius: 5,
        overflow: 'hidden',
        mb: 4,
        minHeight: 340,
        display: 'flex',
        alignItems: 'flex-end',
        '&::before': {
          content: '""',
          position: 'absolute',
          inset: 0,
          backgroundImage: BG_IMAGE,
          backgroundSize: 'cover',
          backgroundPosition: 'center',
          filter: 'brightness(0.4) saturate(0.6)',
        },
        '&::after': {
          content: '""',
          position: 'absolute',
          inset: 0,
          background: 'linear-gradient(180deg, rgba(0,0,0,0.1) 0%, rgba(0,109,119,0.6) 100%)',
        },
      }}
    >
      <Box
        sx={{
          position: 'relative',
          zIndex: 1,
          width: '100%',
          p: { xs: 3, md: 5 },
          display: 'flex',
          flexDirection: { xs: 'column', md: 'row' },
          alignItems: { xs: 'flex-start', md: 'flex-end' },
          justifyContent: 'space-between',
          gap: 3,
        }}
      >
        <Box>
          <Typography
            variant="h3"
            sx={{
              color: '#fff',
              fontWeight: 700,
              fontSize: { xs: '1.5rem', md: '2rem' },
              lineHeight: 1.2,
              mb: 0.5,
            }}
          >
            {user?.firstName ? `${t('common.home')}, ${user.firstName}` : t('dashboard.heroSubtitle', 'Bienvenue sur votre espace santé')}
          </Typography>
          <Typography
            sx={{
              color: 'rgba(255,255,255,0.7)',
              fontSize: { xs: '0.9rem', md: '1.05rem' },
            }}
          >
            {t('dashboard.heroSubtitle', 'Bienvenue sur votre espace santé')}
          </Typography>
        </Box>

        <Box sx={{ display: 'flex', flexWrap: 'wrap', gap: 1.5 }}>
          {actions.map((action) => (
            <Button
              key={action.path}
              variant="contained"
              startIcon={action.icon}
              onClick={() => navigate(action.path)}
              sx={{
                bgcolor: action.color,
                '&:hover': { bgcolor: action.color, filter: 'brightness(0.85)' },
                color: '#fff',
                px: 3,
                py: 1.2,
                borderRadius: 3,
                fontWeight: 600,
                fontSize: '0.85rem',
                backdropFilter: 'blur(8px)',
                boxShadow: '0 4px 15px rgba(0,0,0,0.2)',
              }}
            >
              {action.label}
            </Button>
          ))}
        </Box>
      </Box>
    </Box>
  );
}
