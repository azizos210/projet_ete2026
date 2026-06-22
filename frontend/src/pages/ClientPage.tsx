import { useNavigate } from 'react-router-dom';
import Box from '@mui/material/Box';
import Card from '@mui/material/Card';
import CardContent from '@mui/material/CardContent';
import Typography from '@mui/material/Typography';
import Avatar from '@mui/material/Avatar';
import Button from '@mui/material/Button';
import Grid from '@mui/material/Grid';
import Divider from '@mui/material/Divider';
import Chip from '@mui/material/Chip';
import IconButton from '@mui/material/IconButton';
import Tooltip from '@mui/material/Tooltip';
import EmailIcon from '@mui/icons-material/Email';
import PhoneIcon from '@mui/icons-material/Phone';
import CakeIcon from '@mui/icons-material/Cake';
import WcIcon from '@mui/icons-material/Wc';
import BloodtypeIcon from '@mui/icons-material/Bloodtype';
import WarningAmberIcon from '@mui/icons-material/WarningAmber';
import BadgeIcon from '@mui/icons-material/Badge';
import LocalHospitalIcon from '@mui/icons-material/LocalHospital';
import CalendarMonthIcon from '@mui/icons-material/CalendarMonth';
import DescriptionIcon from '@mui/icons-material/Description';
import MedicationIcon from '@mui/icons-material/Medication';
import MessageIcon from '@mui/icons-material/Message';
import SettingsIcon from '@mui/icons-material/Settings';
import LogoutIcon from '@mui/icons-material/Logout';
import DashboardIcon from '@mui/icons-material/Dashboard';
import HomeIcon from '@mui/icons-material/Home';
import FavoriteIcon from '@mui/icons-material/Favorite';
import { useAuth } from '../contexts/AuthContext';

const DEMO_INFOS = {
  nom: 'Ben Ali',
  prenom: 'Amine',
  email: 'amine.benali@email.com',
  telephone: '+216 55 123 456',
  dateNaissance: '15/03/1990',
  age: 36,
  genre: 'Homme',
  groupeSanguin: 'A+',
  allergies: 'Pénicilline, Aspirine',
  medecinTraitant: 'Dr. Khaled Mejri',
  numDossier: 'DOS-2024-0042',
  assurance: 'CNSS',
  numeroSecuriteSociale: '090 123 456 789',
  adresse: '12 Rue de la Liberté, Tunis 1000',
  contactUrgence: 'Fatma Ben Ali — +216 98 765 432',
};

const SIDEBAR_BTNS = [
  { icon: <HomeIcon />, label: 'Accueil', action: 'home' },
  { icon: <CalendarMonthIcon />, label: 'RDV', action: 'rdv' },
  { icon: <DescriptionIcon />, label: 'Dossier', action: 'dossier' },
  { icon: <MedicationIcon />, label: 'Traitements', action: 'traitements' },
  { icon: <MessageIcon />, label: 'Messages', action: 'messages' },
  { icon: <SettingsIcon />, label: 'Paramètres', action: 'settings' },
];

export default function ClientPage() {
  const { user, logout } = useAuth();
  const navigate = useNavigate();

  const handleLogout = () => {
    logout();
    navigate('/login', { replace: true });
  };

  return (
    <Box sx={{ display: 'flex', minHeight: '100vh', bgcolor: '#F5F7FA' }}>
      {/* Sidebar */}
      <Box sx={{
        width: 80,
        bgcolor: '#006D77',
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'center',
        py: 2,
        gap: 1,
        boxShadow: '4px 0 20px rgba(0,0,0,0.1)',
        position: 'fixed',
        left: 0,
        top: 0,
        bottom: 0,
        zIndex: 10,
      }}>
        <Avatar sx={{ width: 44, height: 44, bgcolor: 'rgba(255,255,255,0.15)', fontSize: 16, fontWeight: 600, color: '#fff', mb: 1 }}>
          {user?.firstName?.[0]}{user?.lastName?.[0]}
        </Avatar>
        <Divider sx={{ width: '60%', borderColor: 'rgba(255,255,255,0.15)', mb: 1 }} />
        {SIDEBAR_BTNS.map((btn) => (
          <Tooltip key={btn.action} title={btn.label} placement="right">
            <IconButton
              onClick={() => btn.action === 'home' ? navigate('/app/dashboard') : btn.action === 'dossier' ? navigate('/app/patient/dossier') : btn.action === 'rdv' ? navigate('/app/patient/rdv') : null}
              sx={{ color: 'rgba(255,255,255,0.7)', '&:hover': { color: '#fff', bgcolor: 'rgba(255,255,255,0.12)' }, width: 48, height: 48 }}
            >
              {btn.icon}
            </IconButton>
          </Tooltip>
        ))}
        <Box sx={{ flexGrow: 1 }} />
        <Divider sx={{ width: '60%', borderColor: 'rgba(255,255,255,0.15)', mb: 1 }} />
        <Tooltip title="Déconnexion" placement="right">
          <IconButton onClick={handleLogout} sx={{ color: 'rgba(255,255,255,0.6)', '&:hover': { color: '#fff', bgcolor: 'rgba(255,255,255,0.12)' } }}>
            <LogoutIcon />
          </IconButton>
        </Tooltip>
      </Box>

      {/* Main Content */}
      <Box sx={{ flexGrow: 1, ml: '80px', position: 'relative', overflow: 'hidden' }}>
        <Box sx={{
          position: 'fixed', inset: 0, ml: '80px', zIndex: 0,
          backgroundImage: 'url(/images/aa.png)',
          backgroundSize: 'cover', backgroundPosition: 'center', backgroundAttachment: 'fixed',
          filter: 'brightness(0.4) saturate(1.1) blur(1px)',
          '&::after': {
            content: '""',
            position: 'absolute', inset: 0,
            background: 'linear-gradient(135deg, rgba(0,109,119,0.3) 0%, rgba(0,0,0,0.5) 100%)',
          },
          animation: 'bgPan 25s ease-in-out infinite alternate',
          '@keyframes bgPan': {
            '0%': { backgroundPosition: 'center top', transform: 'scale(1)' },
            '100%': { backgroundPosition: 'center bottom', transform: 'scale(1.08)' },
          },
        }} />

        <Box sx={{ position: 'relative', zIndex: 1, p: 4, maxWidth: 1000, mx: 'auto' }}>
          {/* Header */}
          <Box sx={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', mb: 3, flexWrap: 'wrap', gap: 2 }}>
            <Box>
              <Typography variant="h4" sx={{ fontWeight: 700, color: '#fff', textShadow: '0 2px 10px rgba(0,0,0,0.3)' }}>
                <FavoriteIcon sx={{ fontSize: 32, mr: 1, verticalAlign: 'middle' }} />
                Mon Profil Santé
              </Typography>
              <Typography variant="body1" sx={{ color: 'rgba(255,255,255,0.75)' }}>
                Bienvenue sur votre espace personnel
              </Typography>
            </Box>
            <Button variant="contained" startIcon={<DashboardIcon />} onClick={() => navigate('/app/patient/dashboard')}
              sx={{ bgcolor: 'rgba(255,255,255,0.15)', backdropFilter: 'blur(8px)', '&:hover': { bgcolor: 'rgba(255,255,255,0.25)' }, borderRadius: 2 }}>
              Tableau de bord
            </Button>
          </Box>

          {/* Profile Card */}
          <Card sx={{ borderRadius: 4, backdropFilter: 'blur(16px)', bgcolor: 'rgba(255,255,255,0.92)', boxShadow: '0 8px 40px rgba(0,0,0,0.15)', overflow: 'visible' }}>
            <CardContent sx={{ p: 4 }}>
              <Box sx={{ display: 'flex', alignItems: 'center', gap: 3, mb: 3, flexWrap: 'wrap' }}>
                <Avatar sx={{ width: 80, height: 80, bgcolor: '#006D77', fontSize: 28, fontWeight: 600, boxShadow: '0 4px 15px rgba(0,109,119,0.3)' }}>
                  {DEMO_INFOS.prenom[0]}{DEMO_INFOS.nom[0]}
                </Avatar>
                <Box sx={{ flex: 1 }}>
                  <Typography variant="h5" sx={{ fontWeight: 700 }}>{DEMO_INFOS.prenom} {DEMO_INFOS.nom}</Typography>
                  <Box sx={{ display: 'flex', gap: 0.5, flexWrap: 'wrap', mt: 0.5 }}>
                    <Chip icon={<BadgeIcon />} label={`Dossier N° ${DEMO_INFOS.numDossier}`} size="small" variant="outlined" color="primary" />
                    <Chip icon={<LocalHospitalIcon />} label={DEMO_INFOS.medecinTraitant} size="small" variant="outlined" color="info" />
                  </Box>
                </Box>
                <Chip icon={<FavoriteIcon />} label={DEMO_INFOS.groupeSanguin} sx={{ bgcolor: '#E53935', color: '#fff', fontWeight: 600, px: 1 }} />
              </Box>

              <Divider sx={{ mb: 3 }} />

              <Grid container spacing={2.5}>
                <Grid size={{ xs: 12, sm: 6, md: 4 }}>
                  <InfoRow icon={<EmailIcon />} label="Email" value={DEMO_INFOS.email} />
                </Grid>
                <Grid size={{ xs: 12, sm: 6, md: 4 }}>
                  <InfoRow icon={<PhoneIcon />} label="Téléphone" value={DEMO_INFOS.telephone} />
                </Grid>
                <Grid size={{ xs: 12, sm: 6, md: 4 }}>
                  <InfoRow icon={<CakeIcon />} label="Date de naissance" value={`${DEMO_INFOS.dateNaissance} (${DEMO_INFOS.age} ans)`} />
                </Grid>
                <Grid size={{ xs: 12, sm: 6, md: 4 }}>
                  <InfoRow icon={<WcIcon />} label="Genre" value={DEMO_INFOS.genre} />
                </Grid>
                <Grid size={{ xs: 12, sm: 6, md: 4 }}>
                  <InfoRow icon={<BloodtypeIcon />} label="Groupe sanguin" value={DEMO_INFOS.groupeSanguin} />
                </Grid>
                <Grid size={{ xs: 12, sm: 6, md: 4 }}>
                  <InfoRow icon={<WarningAmberIcon />} label="Allergies" value={DEMO_INFOS.allergies} color="#E53935" />
                </Grid>
                <Grid size={{ xs: 12 }}>
                  <InfoRow icon={<BadgeIcon />} label="N° Sécurité sociale" value={DEMO_INFOS.numeroSecuriteSociale} />
                </Grid>
                <Grid size={{ xs: 12 }}>
                  <InfoRow icon={<HomeIcon />} label="Adresse" value={DEMO_INFOS.adresse} />
                </Grid>
                <Grid size={{ xs: 12 }}>
                  <InfoRow icon={<PhoneIcon />} label="Contact d'urgence" value={DEMO_INFOS.contactUrgence} />
                </Grid>
              </Grid>
            </CardContent>
          </Card>
        </Box>
      </Box>
    </Box>
  );
}

function InfoRow({ icon, label, value, color }: { icon: React.ReactNode; label: string; value: string; color?: string }) {
  return (
    <Box sx={{ display: 'flex', alignItems: 'center', gap: 1, p: 1.5, borderRadius: 2, bgcolor: 'rgba(0,0,0,0.02)', '&:hover': { bgcolor: 'rgba(0,109,119,0.04)' } }}>
      <Avatar sx={{ width: 34, height: 34, bgcolor: color ? `${color}15` : 'rgba(0,109,119,0.08)', color: color || '#006D77' }}>
        {icon}
      </Avatar>
      <Box sx={{ minWidth: 0 }}>
        <Typography variant="caption" color="text.secondary" sx={{ display: 'block', fontSize: 11 }}>{label}</Typography>
        <Typography variant="body2" sx={{ fontWeight: 500, color: color || 'inherit' }}>{value}</Typography>
      </Box>
    </Box>
  );
}
