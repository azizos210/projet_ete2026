import { useState } from 'react';
import { useNavigate, Link as RouterLink } from 'react-router-dom';
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
import Link from '@mui/material/Link';
import Divider from '@mui/material/Divider';
import LocalHospitalIcon from '@mui/icons-material/LocalHospital';
import PersonIcon from '@mui/icons-material/Person';
import EmailIcon from '@mui/icons-material/Email';
import LockIcon from '@mui/icons-material/Lock';
import Visibility from '@mui/icons-material/Visibility';
import VisibilityOff from '@mui/icons-material/VisibilityOff';
import { authApi } from '../../api/services';

export default function RegisterPage() {
  const [firstName, setFirstName] = useState('');
  const [lastName, setLastName] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');
  const [success, setSuccess] = useState('');
  const navigate = useNavigate();
  const { t } = useTranslation();

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    setSuccess('');
    setLoading(true);
    try {
      await authApi.register({ email, password, firstName, lastName });
      setSuccess(t('auth.registerSuccess'));
      setTimeout(() => navigate('/login'), 2000);
    } catch (err: any) {
      setError(err?.response?.data?.message || err?.response?.data?.detail || t('common.error'));
    } finally {
      setLoading(false);
    }
  };

  return (
    <Box
      sx={{
        minHeight: '100vh',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        background: 'linear-gradient(135deg, #006D77 0%, #83C5BE 100%)',
        p: 2,
      }}
    >
      <Card sx={{ maxWidth: 440, width: '100%', borderRadius: 4 }}>
        <CardContent sx={{ p: 4 }}>
          <Box sx={{ textAlign: 'center', mb: 3 }}>
            <LocalHospitalIcon sx={{ fontSize: 48, color: '#006D77', mb: 1 }} />
            <Typography variant="h4" sx={{ fontWeight: 700, color: '#006D77' }}>
              Hôpital
            </Typography>
            <Typography variant="h6" sx={{ fontWeight: 400, color: 'text.secondary', mt: 1 }}>
              {t('auth.createAccount')}
            </Typography>
            <Typography variant="body2" color="text.secondary">
              {t('auth.registerSubtitle')}
            </Typography>
          </Box>

          {error && <Alert severity="error" sx={{ mb: 2 }}>{error}</Alert>}
          {success && <Alert severity="success" sx={{ mb: 2 }}>{success}</Alert>}

          <Box component="form" onSubmit={handleSubmit}>
            <TextField
              fullWidth
              label={t('auth.firstName', 'Prénom')}
              value={firstName}
              onChange={(e) => setFirstName(e.target.value)}
              required
              autoFocus
              sx={{ mb: 2 }}
              slotProps={{
                input: {
                  startAdornment: <InputAdornment position="start"><PersonIcon color="action" /></InputAdornment>,
                },
              }}
            />
            <TextField
              fullWidth
              label={t('auth.lastName', 'Nom')}
              value={lastName}
              onChange={(e) => setLastName(e.target.value)}
              required
              sx={{ mb: 2 }}
              slotProps={{
                input: {
                  startAdornment: <InputAdornment position="start"><PersonIcon color="action" /></InputAdornment>,
                },
              }}
            />
            <TextField
              fullWidth
              label={t('auth.email')}
              type="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              required
              sx={{ mb: 2 }}
              slotProps={{
                input: {
                  startAdornment: <InputAdornment position="start"><EmailIcon color="action" /></InputAdornment>,
                },
              }}
            />
            <TextField
              fullWidth
              label={t('auth.password')}
              type={showPassword ? 'text' : 'password'}
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              required
              sx={{ mb: 2 }}
              slotProps={{
                input: {
                  startAdornment: <InputAdornment position="start"><LockIcon color="action" /></InputAdornment>,
                  endAdornment: (
                    <InputAdornment position="end">
                      <IconButton onClick={() => setShowPassword(!showPassword)} edge="end">
                        {showPassword ? <VisibilityOff /> : <Visibility />}
                      </IconButton>
                    </InputAdornment>
                  ),
                },
              }}
            />
            <Button
              type="submit"
              fullWidth
              variant="contained"
              size="large"
              disabled={loading}
              sx={{ py: 1.5, fontSize: 16 }}
            >
              {loading ? t('common.loading') : t('auth.register')}
            </Button>
          </Box>

          <Divider sx={{ my: 3 }}>
            <Typography variant="body2" color="text.secondary">OU</Typography>
          </Divider>

          <Box sx={{ textAlign: 'center' }}>
            <Typography variant="body2" color="text.secondary">
              {t('auth.haveAccount', 'Déjà un compte ?')}{' '}
              <Link component={RouterLink} to="/login" sx={{ fontWeight: 600 }}>
                {t('auth.login')}
              </Link>
            </Typography>
          </Box>
        </CardContent>
      </Card>
    </Box>
  );
}
