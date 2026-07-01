import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import Box from '@mui/material/Box';
import Card from '@mui/material/Card';
import CardContent from '@mui/material/CardContent';
import Typography from '@mui/material/Typography';
import TextField from '@mui/material/TextField';
import Button from '@mui/material/Button';
import Avatar from '@mui/material/Avatar';
import Grid from '@mui/material/Grid';
import Divider from '@mui/material/Divider';
import Chip from '@mui/material/Chip';
import Alert from '@mui/material/Alert';
import Snackbar from '@mui/material/Snackbar';
import CircularProgress from '@mui/material/CircularProgress';
import PersonIcon from '@mui/icons-material/Person';
import EmailIcon from '@mui/icons-material/Email';
import BadgeIcon from '@mui/icons-material/Badge';
import SaveIcon from '@mui/icons-material/Save';
import ArrowBackIcon from '@mui/icons-material/ArrowBack';
import { useAuth } from '../contexts/AuthContext';
import { profileApi } from '../api/services';

export default function ProfilePage() {
  const { t } = useTranslation();
  const navigate = useNavigate();
  const { user, hasRole } = useAuth();

  const [editing, setEditing] = useState(false);
  const [saving, setSaving] = useState(false);
  const [snack, setSnack] = useState<{ open: boolean; message: string; severity: 'success' | 'error' }>({ open: false, message: '', severity: 'success' });
  const [form, setForm] = useState({
    firstName: user?.firstName || '',
    lastName: user?.lastName || '',
    email: user?.email || '',
  });

  const roleLabel = user?.roles?.includes('ROLE_ADMIN') ? 'Administrateur'
    : user?.roles?.includes('ROLE_MEDECIN') ? 'Médecin'
    : user?.roles?.includes('ROLE_PATIENT') ? 'Patient'
    : user?.roles?.includes('ROLE_INFIRMIER') ? 'Infirmier'
    : 'Utilisateur';

  const handleSave = async () => {
    setSaving(true);
    try {
      const updated = await profileApi.update(form);
      localStorage.setItem('user', JSON.stringify(updated));
      window.dispatchEvent(new Event('storage'));
      setSnack({ open: true, message: 'Profil mis à jour avec succès', severity: 'success' });
      setEditing(false);
    } catch {
      setSnack({ open: true, message: 'Erreur lors de la mise à jour du profil', severity: 'error' });
    } finally {
      setSaving(false);
    }
  };

  return (
    <Box sx={{ p: 3, maxWidth: 800, mx: 'auto' }}>
      <Button startIcon={<ArrowBackIcon />} onClick={() => navigate(-1)} sx={{ mb: 2 }}>{t('common.back', 'Retour')}</Button>

      <Card sx={{ borderRadius: 4 }}>
        <CardContent sx={{ p: 4 }}>
          <Box sx={{ display: 'flex', alignItems: 'center', gap: 3, mb: 3, flexWrap: 'wrap' }}>
            <Avatar sx={{ width: 80, height: 80, bgcolor: '#006D77', fontSize: 28, fontWeight: 600 }}>
              {user?.firstName?.[0]}{user?.lastName?.[0]}
            </Avatar>
            <Box sx={{ flex: 1 }}>
              <Typography variant="h5" sx={{ fontWeight: 700 }}>{user?.firstName} {user?.lastName}</Typography>
              <Typography variant="body2" color="text.secondary" sx={{ display: 'flex', alignItems: 'center', gap: 0.5, mt: 0.5 }}>
                <EmailIcon sx={{ fontSize: 16 }} /> {user?.email}
              </Typography>
              <Chip label={roleLabel} size="small" color="primary" variant="outlined" sx={{ mt: 1 }} />
            </Box>
            <Button
              variant={editing ? 'outlined' : 'contained'}
              color={editing ? 'error' : 'primary'}
              onClick={() => editing ? setEditing(false) : setEditing(true)}
            >
              {editing ? 'Annuler' : 'Modifier le profil'}
            </Button>
          </Box>

          <Divider sx={{ mb: 3 }} />

          <Grid container spacing={3}>
            <Grid size={{ xs: 12, sm: 6 }}>
              <TextField
                fullWidth
                label="Prénom"
                value={form.firstName}
                onChange={(e) => setForm({ ...form, firstName: e.target.value })}
                disabled={!editing}
                slotProps={{ input: { startAdornment: <PersonIcon sx={{ mr: 1, color: 'text.secondary', fontSize: 20 }} /> } }}
              />
            </Grid>
            <Grid size={{ xs: 12, sm: 6 }}>
              <TextField
                fullWidth
                label="Nom"
                value={form.lastName}
                onChange={(e) => setForm({ ...form, lastName: e.target.value })}
                disabled={!editing}
                slotProps={{ input: { startAdornment: <BadgeIcon sx={{ mr: 1, color: 'text.secondary', fontSize: 20 }} /> } }}
              />
            </Grid>
            <Grid size={{ xs: 12 }}>
              <TextField
                fullWidth
                label="Email"
                type="email"
                value={form.email}
                onChange={(e) => setForm({ ...form, email: e.target.value })}
                disabled={!editing}
                slotProps={{ input: { startAdornment: <EmailIcon sx={{ mr: 1, color: 'text.secondary', fontSize: 20 }} /> } }}
              />
            </Grid>
          </Grid>

          {editing && (
            <Box sx={{ mt: 3, display: 'flex', justifyContent: 'flex-end' }}>
              <Button
                variant="contained"
                color="primary"
                startIcon={saving ? <CircularProgress size={18} color="inherit" /> : <SaveIcon />}
                onClick={handleSave}
                disabled={saving}
              >
                {saving ? 'Enregistrement...' : 'Enregistrer les modifications'}
              </Button>
            </Box>
          )}
        </CardContent>
      </Card>

      {hasRole('ROLE_PATIENT') && (
        <Card sx={{ mt: 3, borderRadius: 4 }}>
          <CardContent sx={{ p: 4 }}>
            <Typography variant="h6" sx={{ fontWeight: 600, mb: 2 }}>Informations médicales</Typography>
            <Typography variant="body2" color="text.secondary">
              Ces informations sont gérées par votre médecin traitant. Contactez votre médecin pour les modifier.
            </Typography>
          </CardContent>
        </Card>
      )}

      <Snackbar open={snack.open} autoHideDuration={4000} onClose={() => setSnack({ ...snack, open: false })} anchorOrigin={{ vertical: 'bottom', horizontal: 'center' }}>
        <Alert severity={snack.severity} variant="filled">{snack.message}</Alert>
      </Snackbar>
    </Box>
  );
}
