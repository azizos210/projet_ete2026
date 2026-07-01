import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import Box from '@mui/material/Box';
import Card from '@mui/material/Card';
import CardContent from '@mui/material/CardContent';
import TextField from '@mui/material/TextField';
import Button from '@mui/material/Button';
import Typography from '@mui/material/Typography';
import Alert from '@mui/material/Alert';
import InputAdornment from '@mui/material/InputAdornment';
import IconButton from '@mui/material/IconButton';
import ToggleButton from '@mui/material/ToggleButton';
import ToggleButtonGroup from '@mui/material/ToggleButtonGroup';
import CircularProgress from '@mui/material/CircularProgress';
import Dialog from '@mui/material/Dialog';
import DialogContent from '@mui/material/DialogContent';
import PersonIcon from '@mui/icons-material/Person';
import LocalHospitalIcon from '@mui/icons-material/LocalHospital';
import AdminPanelSettingsIcon from '@mui/icons-material/AdminPanelSettings';
import EmailIcon from '@mui/icons-material/Email';
import LockIcon from '@mui/icons-material/Lock';
import Visibility from '@mui/icons-material/Visibility';
import VisibilityOff from '@mui/icons-material/VisibilityOff';
import FavoriteIcon from '@mui/icons-material/Favorite';
import { useAuth } from '../../contexts/AuthContext';
import { authApi } from '../../api/services';

const ROLE_CONFIG: Record<string, { label: string; icon: React.ReactNode; bgImg: string; redirect: string }> = {
  patient: { label: 'Patient', icon: <PersonIcon />, bgImg: '/images/p.png', redirect: '/argon/pages/dashboard.html' },
  medecin: { label: 'Médecin', icon: <LocalHospitalIcon />, bgImg: '/images/m.png', redirect: '/app/dashboard' },
  admin: { label: 'Administrateur', icon: <AdminPanelSettingsIcon />, bgImg: '/images/te.png', redirect: '/back' },
};

export default function LoginPage() {
  const [role, setRole] = useState<string>('patient');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const [error, setError] = useState('');
  const [regOpen, setRegOpen] = useState(false);
  const [regData, setRegData] = useState({ firstName: '', lastName: '', email: '', password: '' });
  const [regError, setRegError] = useState('');
  const [regSuccess, setRegSuccess] = useState('');
  const [regLoading, setRegLoading] = useState(false);
  const { login, loading } = useAuth();
  const navigate = useNavigate();
  const { t } = useTranslation();

  const config = ROLE_CONFIG[role];

  const handleLogin = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    try {
      await login(email.trim(), password);
      if (config.redirect === '/back' || config.redirect === '/argon/pages/dashboard.html') {
        window.location.href = config.redirect;
      } else {
        navigate(config.redirect);
      }
    } catch (err: any) {
      setError(err?.message || t('common.error'));
    }
  };

  const handleRegister = async (e: React.FormEvent) => {
    e.preventDefault();
    setRegError('');
    setRegSuccess('');
    setRegLoading(true);
    try {
      await authApi.register({
        ...regData,
        role: role === 'patient' ? 'ROLE_PATIENT' : role === 'medecin' ? 'ROLE_MEDECIN' : 'ROLE_ADMIN',
      });
      setRegSuccess('Compte créé avec succès ! Vous pouvez maintenant vous connecter.');
      setTimeout(() => { setRegOpen(false); setRegSuccess(''); }, 2000);
    } catch (err: any) {
      setRegError(err?.response?.data?.message || 'Erreur lors de l\'inscription');
    } finally {
      setRegLoading(false);
    }
  };

  return (
    <Box sx={{
      minHeight: '100vh',
      display: 'flex',
      alignItems: 'center',
      justifyContent: 'center',
      position: 'relative',
      overflow: 'hidden',
      '&::before': {
        content: '""',
        position: 'absolute',
        inset: 0,
        backgroundImage: `url(/images/w.png)`,
        backgroundSize: 'cover',
        backgroundPosition: 'center',
        backgroundRepeat: 'no-repeat',
        filter: 'brightness(0.35) saturate(1.2)',
        animation: 'slowZoom 20s ease-in-out infinite alternate',
      },
      '&::after': {
        content: '""',
        position: 'absolute',
        inset: 0,
        background: 'linear-gradient(135deg, rgba(0,109,119,0.6) 0%, rgba(0,0,0,0.4) 100%)',
      },
      '@keyframes slowZoom': {
        '0%': { transform: 'scale(1)' },
        '100%': { transform: 'scale(1.15)' },
      },
    }}>
      <Box sx={{ position: 'relative', zIndex: 1, textAlign: 'center', mb: 4, px: 2 }}>
        <Typography variant="h3" sx={{ fontWeight: 800, color: '#fff', mb: 1, textShadow: '0 2px 20px rgba(0,0,0,0.3)' }}>
          <FavoriteIcon sx={{ fontSize: 36, mr: 1, verticalAlign: 'middle' }} />
          Prenez soin de votre santé
        </Typography>
        <Typography variant="h6" sx={{ color: 'rgba(255,255,255,0.85)', fontWeight: 400, textShadow: '0 1px 10px rgba(0,0,0,0.2)' }}>
          Votre bien-être est notre mission. Accédez à vos soins en toute simplicité.
        </Typography>
      </Box>

      <Card sx={{ maxWidth: 450, width: '100%', borderRadius: 4, position: 'relative', zIndex: 1, backdropFilter: 'blur(12px)', bgcolor: 'rgba(255,255,255,0.92)', boxShadow: '0 20px 60px rgba(0,0,0,0.3)' }}>
        <CardContent sx={{ p: 4 }}>
          <Box sx={{ textAlign: 'center', mb: 3 }}>
            <LocalHospitalIcon sx={{ fontSize: 44, color: '#006D77' }} />
            <Typography variant="h5" sx={{ fontWeight: 700, color: '#006D77', mt: 0.5 }}>Hôpital</Typography>
          </Box>

          <ToggleButtonGroup
            value={role}
            exclusive
            onChange={(_, v) => v && setRole(v)}
            fullWidth
            sx={{ mb: 3, '& .MuiToggleButton-root': { py: 1, borderRadius: '8px !important', mx: 0.3, border: '1px solid rgba(0,0,0,0.08)', textTransform: 'none', '&.Mui-selected': { bgcolor: '#006D77', color: '#fff', '&:hover': { bgcolor: '#005a61' } } } }}
          >
            {Object.entries(ROLE_CONFIG).map(([key, cfg]) => (
              <ToggleButton key={key} value={key}>
                {cfg.icon}
                <Typography variant="caption" sx={{ ml: 0.5, fontWeight: 500 }}>{cfg.label}</Typography>
              </ToggleButton>
            ))}
          </ToggleButtonGroup>

          {error && <Alert severity="error" sx={{ mb: 2 }}>{error}</Alert>}

          <Box component="form" onSubmit={handleLogin}>
            <TextField fullWidth label="Email" type="email" value={email} onChange={(e) => setEmail(e.target.value)} required autoFocus sx={{ mb: 2 }}
              slotProps={{ input: { startAdornment: <InputAdornment position="start"><EmailIcon color="action" /></InputAdornment> } }} />
            <TextField fullWidth label="Mot de passe" type={showPassword ? 'text' : 'password'} value={password} onChange={(e) => setPassword(e.target.value)} required sx={{ mb: 2 }}
              slotProps={{
                input: {
                  startAdornment: <InputAdornment position="start"><LockIcon color="action" /></InputAdornment>,
                  endAdornment: <InputAdornment position="end"><IconButton onClick={() => setShowPassword(!showPassword)} edge="end">{showPassword ? <VisibilityOff /> : <Visibility />}</IconButton></InputAdornment>,
                },
              }} />
            <Button type="submit" fullWidth variant="contained" size="large" disabled={loading} sx={{ py: 1.5, fontSize: 15, borderRadius: 2, bgcolor: '#006D77', '&:hover': { bgcolor: '#005a61' } }}>
              {loading ? <CircularProgress size={22} color="inherit" /> : 'Se connecter'}
            </Button>
          </Box>

          <Box sx={{ textAlign: 'center', mt: 2 }}>
            <Typography variant="body2" color="text.secondary">
              Pas encore de compte ?{' '}
              <Button variant="text" size="small" onClick={() => setRegOpen(true)} sx={{ fontWeight: 600, color: '#006D77', textTransform: 'none' }}>
                Créer un compte
              </Button>
            </Typography>
          </Box>
        </CardContent>
      </Card>

      {/* Registration Dialog */}
      <Dialog open={regOpen} onClose={() => setRegOpen(false)} maxWidth="sm" fullWidth
        slotProps={{ paper: { sx: { borderRadius: 4, overflow: 'hidden' } } }}>
        <Box sx={{ position: 'relative', minHeight: '50vh', display: 'flex', alignItems: 'center', justifyContent: 'center',
          '&::before': {
            content: '""',
            position: 'absolute', inset: 0,
            backgroundImage: `url(${config.bgImg})`,
            backgroundSize: 'cover', backgroundPosition: 'center',
            filter: 'brightness(0.3)',
            animation: 'regZoom 15s ease-in-out infinite alternate',
          },
          '@keyframes regZoom': {
            '0%': { transform: 'scale(1)' },
            '100%': { transform: 'scale(1.1)' },
          },
        }}>
          <DialogContent sx={{ position: 'relative', zIndex: 1, width: '100%', maxWidth: 420 }}>
            <Box sx={{ textAlign: 'center', mb: 2 }}>
              <Typography variant="h5" sx={{ fontWeight: 700, color: '#fff' }}>Créer un compte {ROLE_CONFIG[role].label}</Typography>
              <Typography variant="body2" sx={{ color: 'rgba(255,255,255,0.7)' }}>Rejoignez notre plateforme de soins</Typography>
            </Box>
            {regError && <Alert severity="error" sx={{ mb: 2 }}>{regError}</Alert>}
            {regSuccess && <Alert severity="success" sx={{ mb: 2 }}>{regSuccess}</Alert>}
            <Box component="form" onSubmit={handleRegister}>
              <TextField fullWidth label="Prénom" value={regData.firstName} onChange={(e) => setRegData({ ...regData, firstName: e.target.value })} required sx={{ mb: 1.5, '& .MuiOutlinedInput-root': { bgcolor: 'rgba(255,255,255,0.9)' } }} />
              <TextField fullWidth label="Nom" value={regData.lastName} onChange={(e) => setRegData({ ...regData, lastName: e.target.value })} required sx={{ mb: 1.5, '& .MuiOutlinedInput-root': { bgcolor: 'rgba(255,255,255,0.9)' } }} />
              <TextField fullWidth label="Email" type="email" value={regData.email} onChange={(e) => setRegData({ ...regData, email: e.target.value })} required sx={{ mb: 1.5, '& .MuiOutlinedInput-root': { bgcolor: 'rgba(255,255,255,0.9)' } }} />
              <TextField fullWidth label="Mot de passe" type="password" value={regData.password} onChange={(e) => setRegData({ ...regData, password: e.target.value })} required sx={{ mb: 2, '& .MuiOutlinedInput-root': { bgcolor: 'rgba(255,255,255,0.9)' } }} />
              <Button type="submit" fullWidth variant="contained" size="large" disabled={regLoading} sx={{ py: 1.4, fontSize: 15, borderRadius: 2 }}>
                {regLoading ? <CircularProgress size={22} color="inherit" /> : 'Créer mon compte'}
              </Button>
              <Button fullWidth variant="text" onClick={() => setRegOpen(false)} sx={{ mt: 1, color: 'rgba(255,255,255,0.8)', textTransform: 'none' }}>
                Déjà un compte ? Connectez-vous
              </Button>
            </Box>
          </DialogContent>
        </Box>
      </Dialog>
    </Box>
  );
}
